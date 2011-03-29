<form action='login' method='POST'>
  <table width='100%'>
    <tr>
      <td align='center'>
        <table width='250'>
          <tr>
            <td><?php echo $rid_email; ?></td>
            <td><input type='text' name='email' value='<?php echo $email; ?>'</td>
          <tr>
            <td><?php echo $rid_pwd; ?></td>
            <td><input type='password' name='pwd'></td>
          </tr>
          <tr>
            <td colspan=2 align=center><button type='submit'><?php echo $rid_login; ?></button></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
