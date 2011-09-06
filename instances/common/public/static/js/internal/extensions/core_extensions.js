Core.extend.jsLoader = $LAB;
/**
 * loadScriptsAndInit is the method that load the needcreates the instance of the module and call the init method or load the needed scripts
 * @member Core
 * @author Tomas Corral Casas
 */
Core.extend.loadScriptsAndInit = function(aScripts, onLoad)
{
	if( aScripts.length > 0 )
	{
		Core.jsLoader.script(aScripts).wait(function()
		{
			onLoad();
		});
	}
	else
	{
		onLoad();
	}
	
};