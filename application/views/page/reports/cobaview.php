<?php 

  $file = "demo.xls";
        //    $test = "<table  ><tr><td>Cell 1</td><td>Cell 2</td></tr></table>"; 
           header("Content-type: application/vnd.ms-excel");
           header("Content-Disposition: attachment; filename=$file");
            // echo $test;
            
;?>

<table border="1">
  <thead>
    <tr>
      <th>satu</th>
      <th>dua</th>
      <th>tiga</th>
    </tr>
  </thead>
  <tbody>
  <?php 
    foreach ($data as $row) {
     
    ?>
    <tr>
      <td><?php echo $row->id ;?></td>
      <td><?php echo $row->fullName ;?></td>
      <td><?php echo $row->userSpvId ;?></td>
    </tr>

    <?php } ;?>
  </tbody>
</table>
