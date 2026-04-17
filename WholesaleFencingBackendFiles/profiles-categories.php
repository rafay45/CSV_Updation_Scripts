<?php 
$categories = get_terms(
    array(
        'taxonomy'   => 'category',
        'hide_empty' => false,
        'parent' => 0,
        'include' => array(18523, 18524, 18525, 18526, 18527, 18528,18689), // Replace with your category IDs in the desired order
        'orderby' => 'include'
    )
);
?>
<?php if( !empty( $categories ) ){ ?>
  <?php foreach($categories as $category){ 
	$sub_categories_l_1 = get_terms(
		array(
			'taxonomy'   => 'product_category',
			'hide_empty' => false,
			'parent' => $category->term_id
		)
	);
?>
  
	<!--  Single Category -->
    
	<button class="accordion main-categories <?php echo $category->name; ?>"><?php echo $category->name; ?></button>
	<div class="accordion-content">
	<?php if($category->term_id==18523){?> 
	 <div class="subcategory-div wsProductAjaxList" data-maincat="<?php echo $category->term_id; ?>">
		 <?php get_template_part('templates/parts/profiles-filters/vinyl-privacy-with-lattice-top'); ?>  
     </div>
	<?php }elseif($category->term_id==18524){ ?> 
	<div class="subcategory-div wsProductAjaxList" data-maincat="<?php echo $category->term_id; ?>">
		 <?php get_template_part('templates/parts/profiles-filters/vinyl-privacy'); ?> 
		 </div>
	<?php }elseif($category->term_id==18525){ ?> 
	<div class="subcategory-div wsProductAjaxList" data-maincat="<?php echo $category->term_id; ?>">
		 <?php get_template_part('templates/parts/profiles-filters/2-3-4-rail-vinyl'); ?> 
		 </div>
	<?php }elseif($category->term_id==18526){ ?> 
	<div class="subcategory-div wsProductAjaxList" data-maincat="<?php echo $category->term_id; ?>">
		 <?php get_template_part('templates/parts/profiles-filters/vinyl-picket-fence'); ?> 
		 </div>
	<?php }elseif($category->term_id==18527){ ?> 
	<div class="subcategory-div wsProductAjaxList" data-maincat="<?php echo $category->term_id; ?>">
		 <?php get_template_part('templates/parts/profiles-filters/vinyl-pool-fence'); ?> 
		 </div>
	<?php }elseif($category->term_id==18528){ ?> 
	<div class="subcategory-div wsProductAjaxList" data-maincat="<?php echo $category->term_id; ?>">
		 <?php get_template_part('templates/parts/profiles-filters/vinyl-scalloped-picket'); ?> 
		 </div>	 
   <?php }elseif($category->term_id==18689){ ?> 
	<div class="subcategory-div wsProductAjaxList" data-maincat="<?php echo $category->term_id; ?>">
	<div class="sub-categories-grid profile" >
	 	                <div class="sub-cat profile-sub-cats" data-cat-id="4965" data-filter-cat="horizontal-aluminum-filters" data-maincat-id="4964"><img src="https://wholesalefencing.com/invoicing/wp-content/uploads/2024/07/horizontal-modern.jpg"> Aluminum With Fill</div>
                       <div class="sub-cat profile-sub-cats" data-cat-id="4966" data-filter-cat="aluminum-with-fill-filters" data-maincat-id="4964"><img src="https://wholesalefencing.com/invoicing/wp-content/uploads/2024/07/modern-breeze.jpg"> Horizontal Aluminum</div>
      </div>
	     <div class="horizontal-aluminum-filters" data-filter-cat="horizontal-aluminum-filters" style="display:none">
		 <?php get_template_part('templates/parts/profiles-filters/horizontal-aluminum'); ?> 
		 </div>
		 <div class="aluminum-with-fill-filters" data-filter-cat="aluminum-with-fill-filters" style="display:none">
		 <?php get_template_part('templates/parts/profiles-filters/aluminum-with-fill'); ?> 
		 </div>
		 </div>	 
	<?php } ?>
    </div>
	

	<!--  Single Category -->

  <?php } ?>
<?php } ?>