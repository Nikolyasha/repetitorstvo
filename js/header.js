function mobileMenu(){
	if($(".header").height() == 55){
		$(".header").animate({"height": $(".container_header").height()});
	}
	else{
		$(".header").animate({"height": 55});
	}
}