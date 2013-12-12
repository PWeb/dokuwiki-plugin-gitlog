jQuery(function()
{
	jQuery('.dokugitviewerextended .seechanges').on('click', function()
	{
		jQuery(this).next().toggle('show');
	});
});