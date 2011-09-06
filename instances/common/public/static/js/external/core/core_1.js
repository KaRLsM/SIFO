/**
* Copyright 2009-2010 Tomás Corral Casas - All Rights Reserved
*/
/**
 * Core is the class that manages all the modules.
 * Core is the class that has all the different proxies between the framework selected and the Sandbox.
 * Core must be extended to overwrite all the abstract methods.
 * @author Tomas Corral Casas
 * @version 1.0
 * @abstract
 * @class Core
 * @constructor
 * @type Object
 */
var Core = function()
{
	/**
	 * moduleData is the variable that stores all the registered modules
	 * @member Core
	 * @author Tomas Corral Casas
	 * @private
	 * @type Object
	 */
	var moduleData = {};
	/**
	 * debug is the variable that switch the debug mode
	 * @member Core
	 * @author Tomas Corral Casas
	 * @private
	 * @type Boolean
	 */
	var debug = false;
	/**
	 * createInstance is the method that creates the instance of the module when the Core.start is called
	 * @member Core
	 * @author Tomas Corral Casas
	 * @private
	 * @return the instance of the module
	 * @type Object
	 */
	function createInstance(moduleId)
	{
		var instance = moduleData[moduleId].creator(new Sandbox()),
			name,
			method;
		if(!debug)
		{
			for(name in instance)
			{
				method = instance[name];
				if(typeof method == "function")
				{
					instance[name] = function(name, method)
					{
						return function(){
							try{
								return method.apply(this,arguments);
							}
							catch(ex){
								if (typeof console != "undefined" && typeof console.log != "undefined")
								{
									console.log(moduleId + '.' + name + "(): " + ex.message);
								}
							}
						};
					}(name,method);
				}
			}
		}
		return instance;
	}
	return {
		/**
		 * register is the method that stores the module in the private moduleData variable
		 * @member Core
		 * @author Tomas Corral Casas
		 * @param  {string} moduleId This is the id/name of the module to be registered
		 * @param { function} creator This is the function that will be instantiated. <br> <br>Needs a minimum of three members:<br><br>  "init" => This will be called when starting the module<br> "handleNotification" => This will be called when one notification is listened by the module<br> "destroy" => This will be called when stopping the module
		 */
		register: function (moduleId,creator)
		{
			moduleData[moduleId] = {
				creator: creator,
				instance: null
			};
		},
		/**
		 * start is the method that creates the instance of the module and call the init method
		 * @member Core
		 * @author Tomas Corral Casas (Base of Tomas Corral Casas start function)
		 * @param  {string} moduleId This is the id/name of the module to be started
		 */
		start: function(moduleId)
		{
			moduleData[moduleId].instance = createInstance(moduleId);//moduleData[moduleId].creator(new Sandbox());
			if( typeof moduleData[moduleId].instance.getRequiredScripts === 'function')
			{
				this.loadScriptsAndInit( moduleData[moduleId].instance.getRequiredScripts(), moduleData[moduleId].instance.init );
			}
			else
			{
				moduleData[moduleId].instance.init();
			}
		},
		/**
		 * stop is the method that destroy the instance of the module and call the destroy method
		 * @member Core
		 * @author Tomas Corral Casas
		 * @param {string} moduleId This is the id/name of the module to be stopped
		 */
		stop: function(moduleId)
		{
			var data = moduleData[moduleId];
			if(typeof data != "undefined" && typeof data.instance != "undefined")
			{
				data.instance.destroy();
				data.instance = null;
			}
		},
		/**
		 * startAll is the method that start all the modules registered in the Core
		 * @member Core
		 * @author Tomas Corral Casas
		 */
		startAll: function()
		{
			for(var moduleId in moduleData)
			{
				if(moduleData.hasOwnProperty(moduleId))
				{
					this.start(moduleId);
				}
			}
		},
		/**
		 * stopAll is the method that stops all the modules registered in the Core
		 * @member Core
		 * @author Tomas Corral Casas
		 */
		stopAll: function()
		{
			for(var moduleId in moduleData)
			{
				if(moduleData.hasOwnProperty(moduleId))
				{
					this.stop(moduleId);
				}
			}
		},
		/**
		 * ajaxCall is a proxy method that must be overwritten to use Ajax in the application.
		 * ajaxCall is used in the Sandbox class.
		 * @member Core
		 * @author Tomas Corral Casas
		 * @abstract
		 * @param  {string} sURL This is the url where the ajaxCall will call
		 * @param {object}  oData This is an object with all the data to pass the page
		 * @param {object} oHanderls This is the object that encapsulates the handlers for the response
		 * @param {string} sDataType This is the dataType that will be returned
		 */
		ajaxCall: function(sURL, oData, oHandlers,sDataType)
		{
			throw new Error("The Core.ajaxCall must be overwritten to make it work!");
		}
	};
}();
Core.extend = Core;
