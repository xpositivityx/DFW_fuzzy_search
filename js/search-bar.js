jQuery(document).ready(function(){
    $('#search_bar').autocomplete({
        source: function( request, response ) {
            $.ajax({
                url : search.url,
                dataType: "json",
                data: {
                    crit: request.term,
                },
                success: function( data ) {
                    console.log(data);
                  response( $.map( data, function( item ) {
                    arr = item.split(',');
                    return {
                        label: arr[0],
                        value: arr[1]
                    }
                  }));
                }
            });
        },
      autoFocus: true,
      minLength: 0        
    });

    $('#search_bar').keyup(function(event){
        if(event.keyCode == '13'){
            window.location = "/products/"+this.value+"/";
        }
    });

}); 