function sendConversionTracking(type, data){
	if(type!="" && typeof(gtag)==='function' && typeof(data)!=="undefined"){
		var item_id		= "";
		var item_name	= "";
		var item_price	= "";
		var total_price	= "";
		var quantity		= "";
		if(typeof(data.pid)!=="undefined" && data.pid!=="") item_id = data.pid;
		if(typeof(data.name)!=="undefined" && data.name!=="") item_name = data.name;
		if(typeof(data.price)!=="undefined" && data.price!=="") item_price = data.price;
		if(typeof(data.total_price)!=="undefined" && data.total_price!=="") total_price = data.total_price;
		if(typeof(data.quantity)!=="undefined" && data.quantity!=="") quantity = data.quantity;

		if(item_name!="" && item_id!=""){
			gtag('event', type, {'currency': 'KRW','value': total_price,'items': [{ 'item_id':item_id,'item_name':item_name,'price':item_price,'quantity':quantity }]});
		}
	}
}