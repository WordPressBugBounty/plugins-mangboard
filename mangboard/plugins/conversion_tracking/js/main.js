function sendConversionTracking(type, data){
	if(type!="" && typeof(data)!=="undefined"){
		var item_id			= "";
		var item_name		= "";
		var item_price		= "";
		var total_price		= "";
		var quantity			= "";
		var item_category	= "";
		if(typeof(data.pid)!=="undefined" && data.pid!=="") item_id = data.pid;
		if(typeof(data.name)!=="undefined" && data.name!=="") item_name = data.name;
		if(typeof(data.price)!=="undefined" && data.price!=="") item_price = data.price;
		if(typeof(data.total_price)!=="undefined" && data.total_price!=="") total_price = data.total_price;
		if(typeof(data.quantity)!=="undefined" && data.quantity!=="") quantity = data.quantity;
		if(typeof(data.category1)!=="undefined" && data.category1!=="") item_category = data.category1;

		if(item_name!="" && item_id!=""){
			if(typeof(gtag)!=="undefined"){	//google
				gtag('event', type, {'currency':'KRW','value':total_price,'items':[{'item_id':item_id,'item_name':item_name,'price':item_price,'quantity':quantity,'item_category':item_category}]});
			}
			if(typeof(wcs)!=="undefined"){		//naver
				var _conv = {};
					_conv.type = 'add_to_cart';
					_conv.items = [{'id':item_id,'name':item_name,'quantity':quantity,'payAmount':item_price,'category':item_category}];
				wcs.trans(_conv);
			}
			if(typeof(fbq)!=="undefined"){		//facebook pixel
				fbq('track', 'AddToCart',{'content_name':item_name,'content_category':item_category,'content_ids':[item_id],'content_type':'product','value':item_price,'currency':'KRW'});
			}
		}
	}
}