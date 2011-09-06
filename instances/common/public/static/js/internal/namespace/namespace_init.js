Namespace.init = function() {
	$(document).trigger('start.namespace_init');
	if( Namespace.behaviour && Namespace.behaviour.page )
	{
		Namespace.init_page();
		$(document).trigger('end.namespace_init');
	}
	else
	{
		if( Namespace.basePath['behaviours'] )
		{
			Core.jsLoader.script(Namespace.basePath['behaviours']).wait(function()
			{
				Namespace.init_page();
				$(document).trigger('end.namespace_init');
			});
		}
		else
		{
			throw new Error('The behaviours must be exist in Namespace.basePath to make it work!');
		}
	}
};
Namespace.tryToInitPage = false;
Namespace.init_page = function() {
	$(document).trigger('start.namespace_init_page');
	if (typeof Namespace.behaviour.page[document.body.id] == 'function')
	{
		Namespace.behaviour.page[document.body.id]();
	}
	else
	{
		if( Namespace.tryToInitPage)
		{
			throw new Error('We try to init page but it doesn\'t work. Check basePath[behaviour_' + document.body.id + ']');
		}
		else
		{
			if( Namespace.basePath['behaviour_' + document.body.id] )
			{
				Core.jsLoader.script(Namespace.basePath['behaviour_' + document.body.id]).wait(function()
				{
					Namespace.init_page();
				});
			}
			else
			{
				throw new Error('The behaviour_' + document.body.id + ' must be exist in Namespace.basePath to make it work!');
			}
		}
	}
	Namespace.tryToInitPage = true;
	$(document).trigger('end.namespace_init_page');
};
