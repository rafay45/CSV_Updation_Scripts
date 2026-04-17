<?php
if(!session_id()) {           
		session_start();            
	}
	if(isset($_SESSION['ws_cart'])){
	$args=$_SESSION['ws_cart'];
	}
	$customer_address=$args['customer_address']??0; 
/* Template Name: Profiles Template */
get_header();

if(!is_user_logged_in()){
	echo '<script>location.replace("'.site_url('/login').'");</script>';
}

$locations = get_terms(
	array(
		'taxonomy'   => 'warehouse',
		'hide_empty' => false,
	)
);

$current_user_ = wp_get_current_user();
	
?>
<div class="invoice-wrap">
 <div class="invoice-form">
 <div class="personal-info-wrap">
                <img src="https://wholesalefencing.com/invoicing/wp-content/uploads/2024/01/abstract-halftone-dots-background_7095-434.png">
                <h1>Profiles</h1>
 </div>				
    <div class="reviews-wrap fp-wrap">
	<?php get_template_part('templates/parts/profiles-categories'); ?>
    </div>
<style>
 .pdf-container {
    display: flex
;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 93%;
    margin: 0 auto;
}
        .pdf-icon {
            font-size: 24px;
            color: #e74c3c;
        }
        .pdf-info {
            flex-grow: 1;
        }
        .pdf-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .pdf-buttons {
            display: flex;
            gap: 8px;
            width:100%;
            justify-content: center;
        }
        @media (max-width: 767px) {
           .pdf-buttons {
            display: flex;
            gap: 8px;
            width:100%;
        }
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .btn-preview {
            background-color: #646464;
            color: white;
        }
        .btn-preview:hover {
            background-color: #646464;
        }
        .btn-download {
            background-color: #31791d;
            color: white;
        }
        .btn-download:hover {
            background-color: #31791d;
        }
h3.NW-title {
    text-align: center;
    font-size: 24px;
    padding: 20PX;
    font-weight: 600;
}
span.review-link-wrap {
    overflow: hidden;
    padding: 10px;
    border: 1px solid #ccc;
}

.loader-box {
position: fixed;
top: 0;
z-index: 9999;
width: 100%;
height: 100%;
background: #ffffff78;
}
.loader-filter {
border: 6px solid #f3f3f3;
border-radius: 50%;
border-top: 6px solid #3498db;
width: 40px;
height: 40px;
-webkit-animation: spin 2s linear infinite;
animation: spin 2s linear infinite;
transform: translate(-50%, -50%);
position: absolute;
left: 50%;
top: 50%;
}
/ Safari /
@-webkit-keyframes spin {
0% { -webkit-transform: rotate(0deg); }
100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
0% { transform: rotate(0deg);border-top-color: #a80532; }
100% { transform: rotate(360deg);border-top-color: #2e3192; }
}


p, body
{
    font-family: 'DM Sans', sans-serif !important;
}
	
.invoice-wrap {
    max-width: 1200px;
    margin: 0 auto;
	padding-bottom: 40px;
}
	.invoice-location {
    width: 100%;
}
	.invoice-location select {
    height: 36px;
    padding: 0px 10px;
    width: 100%;
    font-family: 'DM Sans';
    max-width: 600px;
    border: 0;
    box-shadow: 0px 0px 3px 0px #a3a3a3;
    border-radius: 5px;
}


.invoice-location {
    text-align: center;
}
	body {
    background-image: url(https://wholesalefencing.com/invoicing/wp-content/uploads/2024/01/pexels-laura-tancredi-7078712-1.jpg);
    background-size: cover;
    background-repeat: no-repeat;
    background-position: top;
    margin: 0;
}
	.invoice-location-main {
    display: flex;
    justify-content: center;
    align-items: center;
}
	.invoice-form h1 {
    margin-bottom: 25px;
    font-size: 23px;
}
	button.next-btn {
    padding: 8px 25px;
    font-family: 'DM Sans';
    font-weight: 600;
    background-color: #278b2c;
    border: 0;
    border-radius: 5px;
    color: #fff;
    margin-top: 29px;
		    cursor: pointer;
}
	.invoice-location img {
    max-width: 200px;
}
	
	.personal-info-wrap, .product-info-wrap {
    text-align: center;
}
.personal-info-wrap img, .product-info-wrap img {
    max-width: 100px;
    margin: 0 auto;
}

	.user-address-form input,textarea#customerNotes {
    height: 36px;
    padding: 0px 10px;
    width: 100%;
    font-family: 'DM Sans';
    max-width: 600px;
    border: 0;
    border-radius: 5px;
    padding: 0;
    text-indent: 12px;
}
input#fileUpload {
    padding: 8px 0px;
}
textarea#customerNotes {
    height: 100%;
	padding-top: 8px !important;
}
	button.next-btn.second-btn {
    margin-top: 20px;
}
	.accordion-content {
    padding: 16px;
    min-height: auto !important;
}
	.sub-categories .sub-category {
    width: 48%;
}
.sub-categories {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.sub-categories select {
    height: 36px;
    padding: 0px 10px;
    width: 100%;
    font-family: 'DM Sans';
    max-width: 600px;
    border: 0;
    box-shadow: 0px 0px 3px 0px #a3a3a3;
    border-radius: 5px;
    padding: 0;
    text-indent: 12px;
}
	.input-field label {
    text-align: left;
    font-family: 'DM Sans';
    font-size: 15px;
    margin-bottom: 3px;
    font-weight: 500;
    color: #403f3f;
}
	.personal-info-wrap {
    padding-top: 100px;
}
.personal-info-wrap {
    display: flex;
    justify-content: center;
    height: auto;
    align-items: center;
    flex-direction: column;
}
.input-field {
    display: grid;
    margin-bottom: 15px;
}
.user-address-form {
    max-width: 600px;
    margin: 0 auto;
    width: 100%;
}
	
	
.products-wrap.highlight, .product-info-wrap.highlight {
    transform: scale(1);
    height: auto;
	transform-origin:top;
}

.full-row > * {
    width: 48%;
}

.full-row {
    display: flex;
    justify-content: space-between;
}
	.product-info-wrap {
    margin-top: 70px;
}

 .invoice-location-main.processed {
    padding-top: 29px;
	 transition: .2s ease-in
}
	
.invoice-location-main.processed .invoice-location h1 {
    display: none;
}
	
.invoice-location-main {
    position: sticky;
    top: 80px;
    height: 100px;
    left: 0;
    right: 0;
    width: 100%;
}	
.invoice-location-main.processed .select-box {
    min-width: 300px;
}
.invoice-location-main.processed .title:before {
    content: "Warehouse Location";
    font-size: 20px;
}
.invoice-location-main.processed .invoice-location {
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    column-gap: 10px;
}
	
.invoice-location-main.processed {
    background-color: #fff;
	    border-bottom-right-radius: 100px;
    border-bottom-left-radius: 100px;
    box-shadow: 0px 0px 6px -1px #c9c4c4;
}	
.invoice-location {
    margin-top: 100px;
}	
.invoice-location {
    max-width: 600px;
    width: 100%;
    box-shadow: 0px 0px 6px -1px #c9c4c4;
    padding: 26px;
	transition.3s ease-in;
	border-radius:10px;
}	
	.invoice-location-main.processed .invoice-location {
    background: none;
    box-shadow: none;
    margin: 0;
		transition.3s ease-in;
}
body button.accordion.is-open + .accordion-content {
    max-height: none !important;
    height: auto;
    padding: 17px;
}
body:After {
    content: "";
    background-color: #ffffff7a;
    position: absolute;
    top: 0;
    width: 100%;
    left: 0;
    right: 0;
    height: 100%;
    z-index: -1;
}
.product-container
	{
		padding-bottom:70px;
	}

button.accordion {
    width: 100%;
    background-color: white;
    border: none;
    outline: none;
    text-align: left;
    padding: 12px 15px;
    font-size: 16px;
	    font-family: 'DM Sans';
    color: #333;
	font-weight:600;
    cursor: pointer;
    transition: background-color 0.2s linear;
    box-shadow: 0px 0px 6px -1px #c9c4c4;
    border-radius: 8px;
}

button.accordion:after {
  font-family: FontAwesome;
  content: "\f13a";
  font-family: "fontawesome";
  font-size: 18px;
  float: right;
}

button.accordion.is-open:after {
  content: "\f139";
}

button.accordion:hover,
button.accordion.is-open {
  background-color: #ddd;
}
button.accordion:hover, button.accordion.is-open {
    background-color: #337921;
    color: #fff;
}
.accordion-content {
    background-color: white;
    border-left: 1px solid whitesmoke;
    border-right: 1px solid whitesmoke;
    padding: 0 20px;
   height: 0;
    overflow: hidden;
    transition: max-height 0.2s ease-in-out;
    box-shadow: 0px 0px 6px -1px #c9c4c4;
    width: 99%;
    margin: 0 auto;
    border-bottom-right-radius: 15px;
    border-bottom-left-radius: 15px;
}
	
	
	
.product-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 96%;
    margin: 0 auto;
}
	
.filter-by i {
    margin-right: 5px;
    font-size: 18px;
}

.filter-by select {
    box-shadow: 0px 0px 3px 0px #a3a3a3;
    border-radius: 5px;
    border: 0;
    padding: 5px;
    font-size: 12px;
    font-weight: 500;
    margin-left: 5px;
    font-family: 'DM Sans';
}	
	.filter-by
	{
		    margin-top: 20px;
    margin-bottom: 10px;
	}
/* added by Haider */
.user-address-form {
    padding: 15px;
    border-radius: 8px;
    max-width: 400px;
    margin: 0 auto;
}

.vendor-title h1 {
    margin: 0 0 15px;
    font-size: 18px;
}

.vendor-info p {
    margin: 8px 0;
}

.input-field {
    margin: 8px 0;
}

.input-field label {
    display: block;
    margin-bottom: 5px;
}

input[type="text"], input[type="email"] {
    width: 100%;
    font-size: 18px;
    padding: 7px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    background: #31791d;
    border: 0;
    color: #fff;
    font-family: 'DM Sans';
    padding: 6px 12px;
    border-radius: 4px;
	cursor:pointer;
}
.pdf-error {
    display: flex;
    align-items: center;
    flex-direction: row;
}
.error-icon {
    padding-right: 10px;
    font-size: 21px;
}
button#cancel-button {
    background-color: #dc3545;
}

button#edit-button {
    background-color: #28a745;
}
p{
	line-height:26px;
}
strong{
	font-weight:bold;
}
form#zipcode-form {
    width: 100%;
    display: flex;
    margin: auto;
	gap:10px
}
button.copy-gmb-link {
    width: 45px;
    margin: auto;
}
</style>

<script>
function downloadPDF(pdfUrl) {
    // Create a temporary anchor element
    const link = document.createElement('a');
    link.href = pdfUrl;
    link.download = pdfUrl.split('/').pop(); // Extracts filename from URL
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Alternative method that works better in some cases
    // window.location.href = pdfUrl + '?download=true';
}
jQuery(document).ready(function($){
	$('.sub-cat[data-subcat-name="All"]').hide();
	const accordionBtns = document.querySelectorAll(".accordion.main-categories");
let activeAccordion = null;

accordionBtns.forEach((accordion) => {
  accordion.onclick = function (e) {
	  var location_id = $('select.wcLocationSelect').val();
    e.preventDefault();
    this.classList.toggle("is-open");
    if($(this).hasClass('Trex') && $(this).hasClass('is-open')) {
           ws_get_filters(5032,'Trex Horizons','',location_id);
        } else {
            // Code to execute if the button does not have the class 'Trex'
            console.log('Other button clicked');
        }
    let content = this.nextElementSibling;

    if (content.style.maxHeight) {
      // This is if the accordion is open
      content.style.maxHeight = null;
      if (activeAccordion === this) {
        activeAccordion = null;
      }
    } else {
      // If the accordion is currently closed
      if (activeAccordion && activeAccordion !== this) {
        // Close the previously active accordion
        activeAccordion.nextElementSibling.style.maxHeight = null;
        activeAccordion.classList.remove("is-open");
		$('.sub-cat').removeClass('active');
        // Custom logic when switching accordions
       $('.pdf-container').css('display','none');
       $('.option').removeClass('selected');
		//alert('cl');
      } else {
        //alert('bl');
      }

      content.style.maxHeight = content.scrollHeight + "px";
      activeAccordion = this;
    }
  };
});

//modern sub-categories click to show desired filter page
//Vinyl filters starts here
$(document).on('click','.profile-sub-cats', function(){
		event.preventDefault();
		var filter_cat=$(this).data('filter-cat');
        $('.sub-cat').removeClass('active');
        if ($(this).hasClass('active')) {
             $(this).removeClass('active');
           }else{
		     $(this).addClass('active');
     		}
		if (filter_cat=='horizontal-aluminum-filters'){
            $('.aluminum-with-fill-filters').css('display','block');
            $('.horizontal-aluminum-filters').css('display','none');
        }else if(filter_cat=='aluminum-with-fill-filters'){
            $('.horizontal-aluminum-filters').css('display','block');
            $('.aluminum-with-fill-filters').css('display','none');
        }
});
//Vinyl filters starts here
$(document).on('click','.vinyl-ranch-rail', function(){
		event.preventDefault();
		
		if ($(this).hasClass('is-open')) {
             $(this).removeClass('is-open');
           }else{
		     $(this).addClass('is-open');
     		}
		
});

 //Vinyl filters starts here
$(document).on('click','.vinyl-panel-width', function(){
		event.preventDefault();
		
		if ($(this).hasClass('is-open')) {
             $(this).removeClass('is-open');
           }else{
		     $(this).addClass('is-open');
     		}
		
});

//Vinyl rail size filter
$(document).on('click','.vinyl-rail-size', function(){
		event.preventDefault();
		
		if ($(this).hasClass('is-open')) {
             $(this).removeClass('is-open');
           }else{
		     $(this).addClass('is-open');
     		}
		
});

//Vinyl picket size filter
$(document).on('click','.vinyl-picket-size', function(){
		event.preventDefault();
		
		if ($(this).hasClass('is-open')) {
             $(this).removeClass('is-open');
           }else{
		     $(this).addClass('is-open');
     		}
		
});

//Vinyl filters starts here
$(document).on('click','.vinyl-body', function(){
		event.preventDefault();
		
		if ($(this).hasClass('is-open')) {
             $(this).removeClass('is-open');
           }else{
		     $(this).addClass('is-open');
     		}
		
});




$('.sp-gap-filter-opt').on('click', function() {
    const selectedGap = $(this).data('value');
    
    // Hide all gap options first
    $('[class^="sp-body-gap-"]').css('display', 'none');
    
    // Show only the selected one
    $(`.sp-body-gap-${selectedGap}-opts`).css('display', 'block');
    $('#semi-privacy-btns-wrap').css('display', 'block');
});

$('.pr-width-filter-opt').on('click', function() {
    const selectedGap = $(this).data('value');
    
    // Hide all gap options first
    $('[class^="pr-body-width-"]').css('display', 'none');
    
    // Show only the selected one
    $('.pr-body-width-6-opts').css('display', 'block');
    $('#privacy-btns-wrap').css('display', 'block');
});

$('.mix-rail-filter-opt').on('click', function() {
    const selectedRail = $(this).data('value');
    if(selectedRail=='3-rail'){
        $('.mx-body-4-rail-opts').css('display', 'none');
        $('.mx-body-3-rail-opts').css('display', 'block');
    }else if(selectedRail=='4-rail'){
        $('.mx-body-4-rail-opts').css('display', 'block');
        $('.mx-body-3-rail-opts').css('display', 'none');
    }
    $('#mix-btns-wrap').css('display', 'block');
});
$('.al-fill-body-filter-opt').on('click', function() {
    $('#al-fill-btns-wrap').css('display', 'block');
});

// Click handler for the View Image button
$('.view-image').on('click', function() {
    // Reset all previous error states
    $('.accordion').css('border', '');
    var cat_id=$(this).attr('data-cat-id');
    var btn_name=$(this).attr('data-btn-name');
    
    if (cat_id == 18689) {
        var filter_group=$(this).data('filter-group');
        if(filter_group=="sp"){
            // Reset all borders first
        $('.sp-gap-filter, .sp-width-filter, .sp-body-filter').css('border', '');
        
        // Check filters in order
        var gapSelected = $('.sp-gap-filter-opt.selected').length > 0;
        var widthSelected = $('.sp-width-filter-opt.selected').length > 0;
        var bodySelected = $('.sp-body-filter-opt.selected').length > 0;
        
        if(!gapSelected) {
            $('.sp-gap-filter').css('border', '2px solid red');
            return false;
        }
        
        if(!widthSelected) {
            $('.sp-width-filter').css('border', '2px solid red');
            return false;
        }
        
        if(!bodySelected) {
            $('.sp-body-filter').css('border', '2px solid red');
            return false;
        }
         gapSelected = $('.sp-gap-filter-opt.selected').data('value');
         widthSelected = $('.sp-width-filter-opt.selected').data('value');
         bodySelected = $('.sp-body-filter-opt.selected').data('value');
        const selectedFilters = {
       gapSelected: gapSelected, // Example category ID
       panel_width: widthSelected, // Example body height value
       body_height: bodySelected,
       filter_keyword: 'semi-privacy',
       cat_id: cat_id
     };
     $('.loader-box').show();
    
    $.ajax({
        url: '<?php echo admin_url("admin-ajax.php"); ?>', // WordPress AJAX URL
        type: 'POST',
        dataType: 'json',
        data: {
            filters: selectedFilters,
            action: 'ws_get_vinyl_profiles'
        },
        success: function(response) {
            console.log('AJAX Response:', response);
            if (response.success && response.data[0].image_url) {
                handleAjaxResponse(response,cat_id,btn_name);
                //$('#vinyl-profile-image').attr('src', response.data[0].image_url).show();
            } else {
                alert('Error: ' + (response.data[0].message || 'No image returned'));
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.responseText);
            alert('AJAX request failed');
        },
        complete: function() {
            $('.loader-box').hide();
        }
    });
        }else if(filter_group=="pr"){
            
            // Reset all borders first
        $('.pr-width-filter, .pr-body-filter').css('border', '');
        
        // Check filters in order
        var widthSelected = $('.pr-width-filter-opt.selected').length > 0;
        var bodySelected = $('.pr-body-filter-opt.selected').length > 0;
        
        if(!widthSelected) {
            $('.pr-width-filter').css('border', '2px solid red');
            return false;
        }
        
        if(!bodySelected) {
            $('.pr-body-filter').css('border', '2px solid red');
            return false;
        }
        
        
         widthSelected = $('.pr-width-filter-opt.selected').data('value');
         bodySelected = $('.pr-body-filter-opt.selected').data('value');
        const selectedFilters = {
       panel_width: widthSelected, // Example body height value
       body_height: bodySelected,
       filter_keyword: 'privacy',
       cat_id: cat_id
     };
     $('.loader-box').show();
    
    $.ajax({
        url: '<?php echo admin_url("admin-ajax.php"); ?>', // WordPress AJAX URL
        type: 'POST',
        dataType: 'json',
        data: {
            filters: selectedFilters,
            action: 'ws_get_vinyl_profiles'
        },
        success: function(response) {
            console.log('AJAX Response:', response);
            if (response.success && response.data[0].image_url) {
                handleAjaxResponse(response,cat_id,btn_name);
                //$('#vinyl-profile-image').attr('src', response.data[0].image_url).show();
            } else {
                alert('Error: ' + (response.data[0].message || 'No image returned'));
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.responseText);
            alert('AJAX request failed');
        },
        complete: function() {
            $('.loader-box').hide();
        }
    });
        // Your logic here...
        }else if(filter_group=="mix"){
            
            // Reset all borders first
        $('.mix-rail-filter, .mix-width-filter, .mix-body-filter').css('border', '');
        
        // Check filters in order
        var railSelected = $('.mix-rail-filter-opt.selected').length > 0;
        var widthSelected = $('.mix-width-filter-opt.selected').length > 0;
        var bodySelected = $('.mix-body-filter-opt.selected').length > 0;
        if(!railSelected) {
            $('.mix-rail-filter').css('border', '2px solid red');
            return false;
        }
        
        if(!widthSelected) {
            $('.mix-width-filter').css('border', '2px solid red');
            return false;
        }
        
        if(!bodySelected) {
            $('.mix-body-filter').css('border', '2px solid red');
            return false;
        }
        
        widthSelected = $('.mix-width-filter-opt.selected').data('value');
         bodySelected = $('.mix-body-filter-opt.selected').data('value');
        const selectedFilters = {
       panel_width: widthSelected, // Example body height value
       body_height: bodySelected,
       filter_keyword: 'mixed',
       cat_id: cat_id
     };
     $('.loader-box').show();
    
    $.ajax({
        url: '<?php echo admin_url("admin-ajax.php"); ?>', // WordPress AJAX URL
        type: 'POST',
        dataType: 'json',
        data: {
            filters: selectedFilters,
            action: 'ws_get_vinyl_profiles'
        },
        success: function(response) {
            console.log('AJAX Response:', response);
            if (response.success && response.data[0].image_url) {
                handleAjaxResponse(response,cat_id,btn_name);
                //$('#vinyl-profile-image').attr('src', response.data[0].image_url).show();
            } else {
                alert('Error: ' + (response.data[0].message || 'No image returned'));
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.responseText);
            alert('AJAX request failed');
        },
        complete: function() {
            $('.loader-box').hide();
        }
    });
        // Your logic here...
        }else if(filter_group=="al-fl"){
            
            // Reset all borders first
        $('.al-fill-body-filter').css('border', '');
        
      
        var bodySelected = $('.al-fill-body-filter-opt.selected').length > 0;
        
        if(!bodySelected) {
            $('.al-fill-body-filter').css('border', '2px solid red');
            return false;
        }
        
        
         bodySelected = $('.al-fill-body-filter-opt.selected').data('value');
        const selectedFilters = {
        // Example body height value
       body_height: bodySelected,
       filter_keyword: 'aluminum-with-fill',
       cat_id: cat_id
     };
     $('.loader-box').show();
    
    $.ajax({
        url: '<?php echo admin_url("admin-ajax.php"); ?>', // WordPress AJAX URL
        type: 'POST',
        dataType: 'json',
        data: {
            filters: selectedFilters,
            action: 'ws_get_vinyl_profiles'
        },
        success: function(response) {
            console.log('AJAX Response:', response);
            if (response.success && response.data[0].image_url) {
                handleAjaxResponse(response,cat_id,btn_name);
                //$('#vinyl-profile-image').attr('src', response.data[0].image_url).show();
            } else {
                alert('Error: ' + (response.data[0].message || 'No image returned'));
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.responseText);
            alert('AJAX request failed');
        },
        complete: function() {
            $('.loader-box').hide();
        }
    });
        // Your logic here...
        }
        
    }
    else{
    // Define filter sections in order (top to bottom)
    // Initialize empty array
    let filterSections = [];

   
    if (cat_id == 18523) {
        filterSections = [];
    filterSections.push(
        {
           section: '.filter-section[data-order="1_1"]',
           container: '.vinyl-body-opts-1', 
           options: '.vinyl-body-opt-1',
           button: '.vinyl-body',
           key: 'body_height'
        },
        { 
        section: '.filter-section[data-order="1_2"]',
        container: '.picket-width-1', 
        options: '.picket-size-opt-1',
        button: '.vinyl-picket-size',
        key: 'picket_size'
    },
        { 
        section: '.filter-section[data-order="1_3"]',
        container: '.rail-size-1', 
        options: '.rail-size-opt-1',
        button: '.vinyl-rail-size',
        key: 'rail_size'
    },
        {
        section: '.filter-section[data-order="1_4"]',
        container: '.lattice-top-rail-size', 
        options: '.top-rail-size-opt-1',
        button: '.vinyl-ranch-rail',
        key: 'top_rail_size'
    },
        { 
        section: '.filter-section[data-order="1_5"]',
        container: '.vinyl-panel-width-1', 
        options: '.panel-width-opt-1',
        button: '.vinyl-panel-width',
        key: 'panel_width'
    }
    );
}else if (cat_id == 18524) {
        filterSections = [];
    filterSections.push(
        {
           section: '.filter-section[data-order="2_1"]',
           container: '.vinyl-body-opts-1', 
           options: '.vinyl-body-opt-1',
           button: '.vinyl-body',
           key: 'body_height'
        },
        { 
        section: '.filter-section[data-order="2_2"]',
        container: '.picket-width-1', 
        options: '.picket-size-opt-1',
        button: '.vinyl-picket-size',
        key: 'picket_size'
    },
        { 
        section: '.filter-section[data-order="2_3"]',
        container: '.rail-size-1', 
        options: '.rail-size-opt-1',
        button: '.vinyl-rail-size',
        key: 'rail_size'
    },
        { 
        section: '.filter-section[data-order="2_4"]',
        container: '.vinyl-panel-width-1', 
        options: '.panel-width-opt-1',
        button: '.vinyl-panel-width',
        key: 'panel_width'
    }
    );
}else if (cat_id == 18526) {
        filterSections = [];
    filterSections.push(
        {
           section: '.filter-section[data-order="4_1"]',
           container: '.vinyl-body-opts-1', 
           options: '.vinyl-body-opt-1',
           button: '.vinyl-body',
           key: 'body_height'
        },
        { 
        section: '.filter-section[data-order="4_2"]',
        container: '.vinyl-panel-width-1', 
        options: '.panel-width-opt-1',
        button: '.vinyl-panel-width',
        key: 'panel_width'
    }
    );
}else if (cat_id == 18527) {
        filterSections = [];
    filterSections.push(
        {
           section: '.filter-section[data-order="5_1"]',
           container: '.vinyl-body-opts-1', 
           options: '.vinyl-body-opt-1',
           button: '.vinyl-body',
           key: 'body_height'
        },
        { 
        section: '.filter-section[data-order="5_2"]',
        container: '.vinyl-panel-width-1', 
        options: '.panel-width-opt-1',
        button: '.vinyl-panel-width',
        key: 'panel_width'
    }
    );
}else if (cat_id == 18528) {
        filterSections = [];
    filterSections.push(
        {
           section: '.filter-section[data-order="6_1"]',
           container: '.vinyl-body-opts-1', 
           options: '.vinyl-body-opt-1',
           button: '.vinyl-body',
           key: 'body_height'
        },
        { 
        section: '.filter-section[data-order="6_2"]',
        container: '.vinyl-panel-width-1', 
        options: '.panel-width-opt-1',
        button: '.vinyl-panel-width',
        key: 'panel_width'
    }
    );
}
    // Object to store all selected values
    const selectedFilters = {};
    let allSelected = true;
    
    // Check each filter in order
    for (let i = 0; i < filterSections.length; i++) {
        const filter = filterSections[i];
        const $selectedOption = $(filter.section).find(filter.container).find(filter.options + '.selected');
        
        if ($selectedOption.length === 0) {
            // Highlight the filter button in red
            $(filter.section).find(filter.button).css('border', '2px solid red');
            
            // Smooth scroll to this filter
            $('html, body').animate({
                scrollTop: $(filter.section).offset().top - 100
            }, 500);
            
            allSelected = false;
            // Focus on the first missing filter only
            break;
        } else {
            // Store the selected value
            selectedFilters[filter.key] = $selectedOption.attr('data-value');
            selectedFilters['cat_id'] = $(this).attr('data-cat-id');
        }
    }
    
    if (allSelected) {
        console.log('All filters selected:', selectedFilters);
        $('.loader-box').show();
        
        // Send AJAX request with the selected filters
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>', // Use localized ajaxurl variable
            type: 'POST',
            dataType: 'json',
            data: {
                filters: selectedFilters,
                action: 'ws_get_vinyl_profiles'
            },
            success: function(response) {
                handleAjaxResponse(response,cat_id,btn_name);
            },
            error: function(xhr, status, error) {
                handleAjaxError(xhr);
            },
            complete: function() {
                $('.loader-box').hide();
            }
        });
    }
        }//end if parent else
});

//rails
$('.2_3_4_rail_btn').on('click', function() {
     $('.loader-box').show();
    var cat_id=$(this).attr('data-cat-id');
    $('.2_3_4_rail_btn').removeClass('selected');
    $(this).addClass('selected');
    if(cat_id=='2r'){
    $('.pdf-title').html('https://wholesalefencing.com/invoicing/wp-content/uploads/2025/03/2-Rail-Vinyl-Fence.pdf');    
    $('.btn-preview').attr("onclick","window.open('https://wholesalefencing.com/invoicing/wp-content/uploads/2025/03/2-Rail-Vinyl-Fence.pdf', '_blank')");
    $('.btn-download').attr("onclick","downloadPDF('https://wholesalefencing.com/invoicing/wp-content/uploads/2025/03/2-Rail-Vinyl-Fence.pdf')");
    }else if(cat_id=='3r'){
    $('.pdf-title').html('https://wholesalefencing.com/invoicing/wp-content/uploads/2025/03/3-Rail-Vinyl-Fence.pdf');
    $('.btn-preview').attr("onclick","window.open('https://wholesalefencing.com/invoicing/wp-content/uploads/2025/03/3-Rail-Vinyl-Fence.pdf', '_blank')");
    $('.btn-download').attr("onclick","downloadPDF('https://wholesalefencing.com/invoicing/wp-content/uploads/2025/03/-Rail-Vinyl-Fence.pdf')");
    }else if(cat_id=='4r'){
    $('.pdf-title').html('https://wholesalefencing.com/invoicing/wp-content/uploads/2025/03/4-Rail-Vinyl-Fence.pdf');
    $('.btn-preview').attr("onclick","window.open('https://wholesalefencing.com/invoicing/wp-content/uploads/2025/03/4-Rail-Vinyl-Fence.pdf', '_blank')");
    $('.btn-download').attr("onclick","downloadPDF('https://wholesalefencing.com/invoicing/wp-content/uploads/2025/03/4-Rail-Vinyl-Fence.pdf')");
     }
    setTimeout(function() {
        $('.loader-box').hide();
    }, 1000);
    $('.pdf-container').css('display','flex');
});
// Handle successful AJAX response
function handleAjaxResponse(response,cat_id,btn_name) {
    console.log('AJAX Response:', response);
    
    if (response.success && response.data && response.data.length > 0) {
        var btn_name=btn_name;
        const profile = response.data[0];
        if(btn_name=='dwn'){
        downloadPDF(profile.image_url);
        }else if(btn_name=='vi'){
            setTimeout(function() {
    window.open(profile.image_url, "_blank");
}, 500);
        }
    } else {
        showErrorState(response.message || 'No profile found matching your criteria.');
    }
}

// Handle AJAX errors
function handleAjaxError(xhr) {
    console.error('AJAX Error:', xhr.responseText);
    showErrorState('Error loading profile. Please try again.');
}

// Show error state
function showErrorState(message) {
    $('.pdf-container')
        .css('display', 'flex')
        .addClass('error-state')
        .html('<div class="pdf-error">'
    + '<div class="error-icon">' + (message.includes("Error") ? '❌' : '⚠️') + '</div>'
    + '<div class="error-message">' + message + '</div>'
    + '</div>');
}
    // When user selects any option, remove red border from its button
    $('.option').on('click', function() {
		// Remove 'selected' from siblings and add to clicked option
        $(this).addClass('selected').siblings('.option').removeClass('selected');
        const $button = $(this).closest('.filter-section').find('.accordion');
        $button.css('border', '');
    });

 });
</script>
<?php
get_footer();

?>