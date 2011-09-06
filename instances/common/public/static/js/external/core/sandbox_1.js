/**
*	© Copyright 2009-2010 Tomás Corral Casas - All Rights Reserved
*/
/**
 * Sandbox is the class that manages all the listeners and notifiers of the application
 * @class Sandbox
 * @author Tomas Corral Casas
 * @constructor
 * @requires Core.js
 */
var Sandbox = function()
{
	return {
		/**
		 * Sandbox.notify is the method that notifies one event to all the modules that listen the event
		 * @member Sandbox
		 * @author Tomas Corral Casas
		 * @param {object} oNotify This is the notifier object that trigger the events on all the modules that listen the notified type. <br><br>Is composed by two different members: <br> "oNotify.type" => {string} This is the type of event that has been triggered<br> "oNotify.data" => {object} This is the object data that will be passed to the triggered function
		 */
		notify: function(oNotify)
		{
			var type = oNotify.type;
			var oAction;
			if(typeof Sandbox.aActions[type] != "undefined")
			{
				for(var nAction = 0, nLenActions = Sandbox.aActions[type].length; nAction < nLenActions; nAction++)
				{
					oAction = Sandbox.aActions[type][nAction];
					oAction.handler.call(oAction.module,oNotify);
				}
			}
		},
		/**
		 * Sandbox.stopListen is the method that stops listening some event/s on any module
		 * @member Sandbox
		 * @author Tomas Corral Casas
		 * @param {array} aNotificationsToStopListen This is the array of events that the module will stop to listen
		 * @param {object} oModule This is the module where all this type of listeneres  will be stopped.
		 */
		stopListen: function(aNotificationsToStopListen,oModule)
		{
			var sNotification;
			var aAuxActions = [];
			for(var nNotification = 0, nLenNotificationsToListen = aNotificationsToStopListen.length; nNotification < nLenNotificationsToListen; nNotification++)
			{
				sNotification = aNotificationsToStopListen[nNotification];

				for(var nAction = 0,nLenActions = Sandbox.aActions[sNotification].length; nAction < nLenActions; nAction++)
				{
					if(oModule != Sandbox.aActions[sNotification][nAction].module)
					{
						aAuxActions.push(Sandbox.aActions[sNotification][nAction]);
					}
				}
				Sandbox.aActions[sNotification] = aAuxActions;
				if(Sandbox.aActions[sNotification].length == 0)
				{
					delete Sandbox.aActions[sNotification];
				}
			}
		},
		/**
		 * Sandbox.listen is the method that starts listening some event/s on any module
		 * @member Sandbox
		 * @author Tomas Corral Casas
		 * @param {array} aNotificationsToListen This the array of events that the module will start to listen
		 * @param {function} fpHandler This is the handler function to manage that must be done when the event is triggered. "handleNotification" by default
		 * @param {object} oModule This is the module where all this type of listeneres will start
		 */
		listen: function(aNotificationsToListen,fpHandler, oModule)
		{
			var sNotification;

			for(var nNotification = 0, nLenNotificationsToListen = aNotificationsToListen.length; nNotification < nLenNotificationsToListen; nNotification++)
			{
				sNotification = aNotificationsToListen[nNotification];
				if(typeof Sandbox.aActions[sNotification] == "undefined")
				{
					Sandbox.aActions[sNotification] = [];
				}
				Sandbox.aActions[sNotification].push({
												module: oModule,
												handler: fpHandler
											});
			}
		},
		/**
		 * Sandbox.request is the method that must be used to make ajax call inside the modules. Is a proxy method
		 * @member Sandbox
		 * @author Tomas Corral Casas
		* @param  {string} sURL This is the url where the ajaxCall will call
		 * @param {object}  oData This is an object with all the data to pass the page
		 * @param {object} oHanderls This is the object that encapsulates the handlers for the response
		 * @param {string} sDataType This is the dataType that will be returned
		 */
		request: function(sUrl, oData, oHandlers,sDatatype)
		{
			Core.ajaxCall(sUrl,oData,oHandlers,sDatatype);
		}
	};
};
/**
 * Sandbox.aActions is the static variable that stores all the listeners of all the modules
 * @author Tomas Corral Casas
 * @type Array
 */
Sandbox.aActions = [];
