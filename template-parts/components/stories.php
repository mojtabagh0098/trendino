<div class="container c-stories">
    <?php 
    $stories = [
        ['img' => 'Ellipse-607.png', 'title' => 'لپتاپ'],
        ['img' => 'Ellipse-608.png', 'title' => 'اسپیکر'],
        ['img' => 'Ellipse-607.png', 'title' => 'لپتاپ'],
        ['img' => 'Ellipse-608.png', 'title' => 'اسپیکر'],
        ['img' => 'Ellipse-607.png', 'title' => 'لپتاپ'],
        ['img' => 'Ellipse-608.png', 'title' => 'اسپیکر'],
        ['img' => 'Ellipse-607.png', 'title' => 'لپتاپ'],
        ['img' => 'Ellipse-608.png', 'title' => 'اسپیکر'],
        ['img' => 'Ellipse-607.png', 'title' => 'لپتاپ'],
        ['img' => 'Ellipse-608.png', 'title' => 'اسپیکر'],
        ['img' => 'Ellipse-607.png', 'title' => 'لپتاپ'],
        ['img' => 'Ellipse-608.png', 'title' => 'اسپیکر'],
        ['img' => 'Ellipse-607.png', 'title' => 'لپتاپ'],
        ['img' => 'Ellipse-608.png', 'title' => 'اسپیکر'],
    ];
    foreach($stories as $story): ?>
    <div class="c-stories__item">
      <div class="c-stories__circle">
        <img src="<?=get_template_directory_uri();?>/assets/img/<?= $story['img'] ?>" />
      </div>
      <span><?= $story['title'] ?></span>
    </div>
    <?php endforeach; ?>
</div>