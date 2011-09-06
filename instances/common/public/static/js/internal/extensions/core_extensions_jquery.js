/**
*	© Copyright 2009-2010 Tomás Corral Casas - All Rights Reserved
*/
/**
 * Core._setAjaxData is a method to convert the data object to Array
 * @member Core
 * @author Tomas Corral Casas
 * @private
 * @param oData
 * @type Object
 * @return the data in a different format - gets and Object and returns an Array
 * @type Array
 */
Core.extend._setAjaxData = function(oData)
{
	var oAux = [],oIterateData;
	for(var nData = 0, nLenData = oData.length; nData < nLenData; nData++)
	{
		oIterateData = oData[nData];
		for(var data in oIterateData)
		{
			if(oIterateData.hasOwnProperty(data))
			{
				oAux.push({ name: data , value: oIterateData[data]});
			}
		}
	}
	return oAux;
};
/**
 * Core.ajaxCall this method overwrites the abstract Core.ajaxCall that is used in Sandbox.
 * Core.ajaxCall must be overwritten with the correct format depending from the JS framework used
 * @member Core
 * @author Tomas Corral Casas
 * @param  {string} sURL This is the url where the ajaxCall will call
 * @param {object}  oData This is an object with all the data to pass the page
 * @param {object} oHanderls This is the object that encapsulates the handlers for the response
 * @param {string} sDataType This is the dataType that will be returned
 */
Core.extend.ajaxCall = function(sURL, oData, oHandlers,sDataType)
{
	var sDataType = sDataType || "json";
	$.ajax({
		url:sURL,
		method:"POST",
		dataType: sDataType,
		data:this.setAjaxData(oData),
		success: function(response)
		{
			oHandlers.success(response);
		},
		error: function(response)
		{
			oHandlers.error(response);
		}
	});
}

/**
 * Core.extendPropertiesElement extend the element's properties with the JS framework used
 * @member Core
 * @author Jorge del Casar
 * @param  {object} settings A set of key/value pairs that configure the Ajax request. All options are optional. (http://api.jquery.com/jQuery.ajax/).
 */
Core.extend.extendPropertiesElement = function( element, context ){
	return jQuery( element, context );
}

Core.extend.$ = Core.extend.extendPropertiesElement;