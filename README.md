EasyViewSyntax implements a view renderer that allows users to use a simple template syntax.
=====================================

To use EasyViewSyntax, configure it as an application component named "viewRenderer" in the application configuration:

    array(
      'components'=>array(
          ......
          'viewRenderer'=>array(
              'class'=>'EasyViewSyntax',
          ),
      ),
    )

EasyViewSyntax allows you to write view files with the following syntax:

  // print variable
  {=$var}
 
  // print expression
  {=$this->createUrl('site/index')}
 
  // foreach loop
  {foreach $arr as $key => $value}
   <p>{$value}</p>
  {/foreach}
 
  // if-elseif-else
  {if $num == 1}
   <span>1</span>
  {elseif $num == 2}
   <span>2</span>
  {else}
   <span>3</span>
  {/if}
 
  // reference a js file in webroot's js dir
  {js:jquery/jquery}
 
  // reference a css file in webroot's css dir
  {css:main}
 
  // no parse, the content between {ignore} and {/ignore} will print directly
  {ignore}
  {if $num == 1}
   <span>1</span>
  {elseif $num == 2}
   <span>2</span>
  {else}
   <span>3</span>
  {/if}
  {/ignore}
