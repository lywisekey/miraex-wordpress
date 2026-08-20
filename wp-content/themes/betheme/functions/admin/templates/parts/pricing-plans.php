<?php
 if( empty($this->pricing) ){
	 return;
 }

// echo '<pre>';
//  print_r($this->pricing);
// echo '</pre>';

echo '<div class="pricing-plans">';

	$regular = 0;

  if( is_array( $this->pricing ) ){
  	foreach( $this->pricing as $plan ){

  		if( ! $regular ){
  			$regular = $plan['price'] / 100;
  		}

  		echo '<div class="pricing-plan '. (!empty($plan['popular']) ? 'highlighted' : '') .'">';

  			$price = $plan['price'] / 100;
  			$per_site = ceil($price / $plan['quantity']);
  			$percent = ceil( ( ($regular - $per_site) / $regular) * 100 );

  			if( ! empty($plan['popular']) ){
  				echo '<span class="badge-popular">Most Popular</span>';
  			}

  			echo '<div class="plan-name">'. $plan['name'] .'</div>';
  			echo '<div class="plan-sub">'. ($plan['quantity'] < 999 ? $plan['quantity'] : 'unlimited') .' website'. ($plan['quantity'] > 1 ? 's' : '') .'</div>';
  			echo '<div>';
  				echo '<span class="price"><sup>$</sup>'. $price .'</span>';
  				echo '<span class="price-period">/year</span>';
  			echo '</div>';
  			if( $plan['quantity'] < 999 ){
  				echo '<div class="price-meta">';
  					echo '<span class="per-site">$'. $per_site .' per site&nbsp;</span>';
  					if( $plan['quantity'] > 1 ){
  						echo '<span class="discount">-'. $percent .'%</span>';
  					}
  				echo '</div>';
  			}
  		echo '</div>';

  	}
  }

echo '</div>';
?>
