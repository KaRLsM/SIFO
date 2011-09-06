<?php
{foreach from=$config item=c key=k}
{if is_array($c)}
{foreach from=$c item=path key=nspace}
{if $path|is_array}
$config['{$k}']['{$nspace}'] = '{$path.0}';
{else}
$config['{$k}']['{$nspace}'] = '{$path}';
{/if}
{/foreach}
{else}
$config['{$k}'] = '{$c}';
{/if}
{/foreach}
?>