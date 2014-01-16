jQuery(function()
{
	jQuery('.gitlogplugin .seechanges').on('click', function(event)
	{
		jQuery(this).next().toggle('show');
		event.preventDefault();
	});
});