<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
 


<!-- <div class="alert <?php echo trim($alertclass) == '' ? 'alert-success' : $alertclass ?>" role="alert">
  <h4 class="alert-heading"><?php echo trim($heading) == '' ? '' : $heading ?></h4>
</div> -->

<style>
  #snackbar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #5f5d5d;
    color: white;
    font-family: sans-serif;
    padding: 15px 20px;
    border-radius: 5px;
    min-width: 300px;
    /* display: none; */
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
  }

  #snackbar.active {
    display: flex !important;
  }

  .close-snackbar {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    box-sizing: content-box;
    padding: 6px;
    border: 2px solid #fff;
    background-color: rgba(0, 0, 0);
    cursor: pointer;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .close-snackbar:hover {
    background-color: rgb(77, 77, 77);
  }
</style>

<?php if(trim($heading) != ''){ ?>

<div id="snackbar">
  <p><?= $heading ?></p>
  <button class="close-snackbar" style="margin-left: 10px;">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="3" stroke="#fff" width="20" height="20">
      <path d="M6 18L18 6M6 6l12 12" />
    </svg>
  </button>
</div>

<?php } ?>

<script>
  alert("<?= $heading ?>")

  document.querySelector('.close-snackbar').onclick = function () {
    document.getElementById('snackbar').style.display = 'none';
  };

</script>