<?php

/**
 * EasyViewSyntax implements a view renderer that allows users to use a simple template syntax.
 *
 * To use EasyViewSyntax, configure it as an application component named "viewRenderer" in the application configuration:
 * <pre>
 * array(
 *     'components'=>array(
 *         ......
 *         'viewRenderer'=>array(
 *             'class'=>'EasyViewSyntax',
 *         ),
 *     ),
 * )
 * </pre>
 *
 * EasyViewSyntax allows you to write view files with the following syntax:
 * <pre>
 * // print variable
 * {=$var}
 *
 * // print expression
 * {=$this->createUrl('site/index')}
 *
 * // foreach loop
 * {foreach $arr as $key => $value}
 *  <p>{$value}</p>
 * {/foreach}
 *
 * // if-elseif-else
 * {if $num == 1}
 *  <span>1</span>
 * {elseif $num == 2}
 *  <span>2</span>
 * {else}
 *  <span>3</span>
 * {/if}
 *
 * // reference a js file in webroot's js dir
 * {js:jquery/jquery}
 *
 * // reference a css file in webroot's css dir
 * {css:main}
 *
 * // no parse, the content between {ignore} and {/ignore} will print directly
 * {ignore}
 * {if $num == 1}
 *  <span>1</span>
 * {elseif $num == 2}
 *  <span>2</span>
 * {else}
 *  <span>3</span>
 * {/if}
 * {/ignore}
 *
 * </pre>
 */

class EasyViewSyntax extends CViewRenderer {
    private $_ignore;
    private $_from;
    private $_to;

    /**
     * Compile the source view file and saves the results as another file.
     * @param string $sourceFile the source view file path
     * @param string $viewFile the resulting view file path
     */
    public function generateViewFile($sourceFile, $viewFile) {
        file_put_contents($viewFile, $this->compile(file_get_contents($sourceFile)));
    }

    /**
     * Compile the content, process every specific tag.
     * @param string $content
     * @return string
     */
    public function compile($content) {
        $this->_ignore = array();
        $content = preg_replace('/{ignore}(.+?){\/ignore}/ise', '$this->addIgnore(\'$1\')', $content);
        $content = preg_replace($this->getFrom(), $this->getTo(), $content);
        if ($this->_ignore) {
            $content = strtr($content, $this->_ignore);
        }
        return $content;
    }

    public function getFrom() {
        if ($this->_from === null) {
            $this->_from = array(
                '/{#/',
                '/#}/',
                '/{if\s+(.+?)}/',
                '/{\/if}/',
                '/{foreach\s+(.+?)}/',
                '/{\/foreach}/',
                '/{else}/',
                '/{else\s*if\s+(.+?)}/',
                '/{=(.+?)}/',
                '/{%=(.+?)%}/',
                '/{css:\s*(.+?)}/',
                '/{js:\s*(.+?)}/',
            );
        }
        return $this->_from;
    }

    public function getTo() {
        if ($this->_to === null) {
            $this->_to = array(
                '<?php /*',
                '*/ ?>',
                '<?php if (\1) { ?>',
                '<?php } ?>',
                '<?php foreach (\1) { ?>',
                '<?php } ?>',
                '<?php } else { ?>',
                '<?php } elseif (\1) { ?>',
                '<?php echo \1; ?>',
                '<?php echo \1; ?>',
                '<?php Yii::app()->clientScript->registerCssFile(Yii::App()->baseUrl . "/css/\1.css"); ?>',
                '<?php Yii::app()->clientScript->registerScriptFile(Yii::App()->baseUrl . "/js/\1.js", CClientScript::POS_END); ?>',
            );
        }
        return $this->_to;
    }

    public function setFrom($from) {
        $this->_from = $from;
    }

    public function setTo($to) {
        $this->_to = $to;
    }

    private function addIgnore($tpl) {
        $tpl = stripcslashes($tpl);
        $key = '<###ignore' . count($this->_ignore) . '###>';
        $this->_ignore[$key] = $tpl;
        return $key;
    }
}
