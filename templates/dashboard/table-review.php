<div class="table-container">
<table class="table default-table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Company</th>
            <th scope="col">Comment</th>
            <th scope="col">Score</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($reviews)):
            foreach ($reviews as $review):

               
                $company_id = carbon_get_post_meta($review->ID, "company");
            
                $score = carbon_get_post_meta($review->ID, "score");
                $score_text = "";
                for ($i = 0; $i < $score; $i++) {
                    $score_text .= '<span class="fa fa-star filled" style="  color: #ffc000;"></span>';
                }
                ?>
			                    <tr>
			                        <th scope="row"><?php echo $review->ID; ?></th>
			                        <td><?php echo esc_html(get_the_title($company_id)); ?></td>
			                        <td><?php echo esc_html($review->post_content); ?></td>
			                        <td><?php echo $score_text; ?></td>
	                    </tr>
	               
            <?php endforeach;?>
        <?php endif;?>
    </tbody>
</table>
</div>


