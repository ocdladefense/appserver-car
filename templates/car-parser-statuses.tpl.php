<style type="text/css">
    .status-table{
        width:75%;
    }
    table, th, td {
        border: 1px solid black;
        border-collapse:collapse;
    }
    .date{
        min-width:5px;
        max-width:5px;
    }
    .e-type{
        min-width:13px;
        max-width:13px;
    }
    .message{
        min-width:25px;
        max-width:25px;
    }
    .runtime{
       min-width:12px;
       max-width:12px;
    }
    .url{
        min-width:25px;
        max-width:25px;
        word-break:break-all;
    }
</style>

<table class="status-table">
    <tr class="status-row">
        <th class="date">Date</th>
        <th class="e-type">Error Type</th>
        <th class="message">Message</th>
        <th class="runtime">Runtime</th>
        <th class="url">Given URL</th>
    </tr>
<!-- </table> -->



<?php
foreach($statuses as $status){
    $date = $status->getDate();
    $message = $status->getMessage();
    $url = $status->getSelectedUrl();
    $statusCode = $status->getStatusCode();
    $runtime = $status->getRuntime();
?>
<table class="status-table">
    <tr class="status-row">
        <td class="date status-field"> <?php print $date; ?> </td>
        <td class="e-type status-field"> <?php print $statusCode; ?> </td>
        <td class="message status-field"> <?php print $message; ?> </td>
        <td class="runtime status-field"> <?php print $runtime; ?> </td>
        <td class="url status-field"><a href="<?php print $url; ?>"><?php print $url; ?></a></td>
    </tr>
</table>
<?php }; ?>