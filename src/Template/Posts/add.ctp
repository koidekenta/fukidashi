<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Post $post
 */
?>
<div class="posts form large-9 medium-8 columns content">
    <?= $this->Form->create($post) ?>
    <fieldset>
        <?php
            echo $this->Form->control('post',['label' => 'つぶやく','rows' => '2','placeholder' => '今、何してる？','style' => 'border-radius:10px;']);
            echo $this->Form->file('post_img',['label' => 'つぶやく','rows' => '2','placeholder' => '今、何してる？','style' => 'border-radius:10px;']);
            echo $this->Form->control('slug',['type' => 'hidden', 'value' => hash("md4",$this->request->session()->read("Auth.User.username").mt_rand().time())]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('つぶやく'),['style' => 'border-radius:10px;']) ?>
    <?= $this->Form->end() ?>
</div>
