/**
 * i18n object to manipulate locales in js code.
 */
function i18n()
{
}
/**
 * Initalize the data containter
 */
i18n.data = {};

/**
 * Setting the main domain for locales: (manager, messages, etc ...)
 * @param {String} domain Name of the domain.
 */
i18n.setDomain = function( domain )
{
	i18n.default_domain = domain;
};

/**
 * Resetting the domain to the default domain (specified in i18n_msgs.js in each instance).
 */
i18n.resetDomain = function()
{
	i18n.default_domain = i18n_default_domain;
};

/**
 * Main function to return the translation.
 *
 * @param {String} msgid Message ID.
 * @param {Array} params Array with parameters for replacements ( %1, %2, etc ...).
 */
i18n.getText = function( msgid, params )
{

	var text = '';

	if ( i18n.data[i18n.default_domain][msgid] != undefined )
	{
		text = i18n.data[i18n.default_domain][msgid];
	}
	else
	{
		text = msgid;
	}

	if (params != undefined)
	{
		var callback = function(v, n) {
			// It's a parameter.
			if ( n >= 1 && ( n <= num_params ) ) {
				return params[n - 1];
			}
			/// Out of bounds. Return the expression as it is.
			else {
				return v;
			}
		};

		var num_params = params.length;
		var pattern = /%(\d+)/g;
		text = text.replace( pattern, callback );
	}
	return text;
};

/**
 * Wrapper function for gettext.
 */
i18n._ = i18n.getText;

/**
 * Method called at the end of the i18n_msgs.js.
 *
 * Defines the default domain and load the translations into the object.
 */
i18n.loadData = function(data)
{
	i18n.data[this.default_domain] = $.extend(i18n.data, data);
};
