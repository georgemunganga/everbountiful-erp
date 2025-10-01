<style>
.alert-dismissible .close {
    top: unset;
    right: -21px;
}
</style>
<?php if ($this->session->flashdata('message')) { ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <?php echo $this->session->flashdata('message') ?>
</div>
<?php } ?>
<?php if ($this->session->flashdata('exception')) { ?>
<div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <?php echo $this->session->flashdata('exception') ?>
</div>
<?php } ?>
<?php if (validation_errors()) { ?>
<div class="alert alert-danger alert-dismissible" role="alert">
    <button style="margin-top: 1rem;" type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <?php echo validation_errors() ?>
</div>
<?php } ?>
<?php 
$this->session->unset_userdata('message');
$this->session->unset_userdata('exception');
?>