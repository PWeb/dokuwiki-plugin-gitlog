jQuery(function()
{
	jQuery('.gitlogplugin .seechanges').on('click', function()
	{
		jQuery(this).next().toggle('show');
	});
});