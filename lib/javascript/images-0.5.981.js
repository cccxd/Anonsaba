
/*jquery... don't cross the streams!*/
	jQuery.noConflict();
	
	var jcrop_api = null;
	function show_preview(coords)
	{
		var ry = 200 / coords.h;
		//var rx = coords.w * ry;
		jQuery('#jcrop-preview-div').css({
		width: Math.round(ry * coords.w) + 'px'
		});
		jQuery('#jcrop-preview').css({
		width: Math.round(ry * image_width) + 'px',
		height: Math.round(ry * image_height) + 'px',
		marginLeft: '-' + Math.round(ry * coords.x) + 'px',
		marginTop: '-' + Math.round(ry * coords.y) + 'px'
		});
		jQuery('#x').val(coords.x);
		jQuery('#y').val(coords.y);
		jQuery('#w').val(coords.w);
		jQuery('#h').val(coords.h);
		jQuery('#shi-width').val(coords.w);
		jQuery('#shi-height').val(coords.h);
	}
	jQuery(function(){
		jcrop_api = jQuery.Jcrop('#jcrop-image', {
		onChange: show_preview,
		onSelect: show_preview,
		aspectRatio: 4/3,
		});
		init_motivator();
	});
	function init_motivator()
	{
		jQuery('#motivator-options').show();
		jQuery('#aspect1').attr('checked','checked');
		jQuery('#jcrop-preview-div').css({
		border: '20px solid #000000',
		borderBottom: '50px solid #000000'
		});
		jcrop_api.setOptions({aspectRatio: 4/3});
		jcrop_api.setSelect([0, 0, image_width, image_height]);
	}
	function init_macro()
	{
		jQuery('#macro-options').show();
		jcrop_api.setOptions({aspectRatio: 0});
		jcrop_api.setSelect([0, 0, image_width, image_height]);
	}
	function init_shi()
	{
		jQuery('#shi-options').show();
		jcrop_api.setOptions({aspectRatio: 0});
		jcrop_api.setSelect([0, 0, image_width, image_height]);
	}
	function update_aspect(opt)
	{
		var ar = 0;
		if (opt.value == 'landscape')
		{
		ar = 4/3;
		}
		if (opt.value == 'portrait')
		{
		ar = 3/4;
		}
		jcrop_api.setOptions({aspectRatio: ar});
		jcrop_api.focus();
	}
	function update_tool(e, obj)
	{
		jQuery('#motivator-options').hide();
		jQuery('#macro-options').hide();
		jQuery('#shi-options').hide();
		jQuery('#jcrop-preview-div').css({
		border: '',
		});
		if (obj.value == 'motivator')
		{
		init_motivator();
		}
		else if (obj.value == 'macro')
		{
		init_macro();
		}
		else
		{
		init_shi();
		}
		return true;
	}
	
			
		var picker = {
			
			colorize: function(){
				new MooRainbow("colorizepicker", {
					id: "moocolorize",
					imgPath: imgpath,
					onChange: function(color){
						picker.setColors("rgbcolorize", "rgbcolorizepreview", color.rgb, color.hex);
					}
				})
			},
				
			fillstroke:	function(){
				new MooRainbow("fillstrokepicker", {
					id: "moofillandstroke",
					imgPath: imgpath, 
					onChange: function(color) {
						var colortype = (jQuery("[name=colortype]:checked").val() == null)? "rgbfill": jQuery("[name=colortype]:checked").val();
						var selector = colortype+"preview";
						picker.setColors(colortype, selector, color.rgb, color.hex);
					}
				})
			},
			
			setColors: function(hiddenfield, previewlayer, crgb, chex){
				document.getElementById(hiddenfield).value = crgb;
				document.getElementById(previewlayer).style.color = chex;
				document.getElementById(previewlayer).innerHTML = crgb;
				return true;
			},
			
			init:	function(){
				picker.colorize();
				picker.fillstroke();
			}
		};
		
		function showFilterOption(selector){
			
			p = "#"+selector+"panel";
			s = jQuery("#"+selector+":checked").val();
			//alert(s);
			
				if(s != undefined){
					jQuery(p).show();
				}
				
				else{
					jQuery(p).hide();
				}
			
		}
		
		 jQuery("document").ready(function(){
		 
			jQuery("#setfiltercolorize").click(function(){
				showFilterOption("setfiltercolorize");
			});
				
			picker.init();
		})
