

<!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

<style>
    .email-container{
        max-width: 60%;
        background-color: lightblue;
        padding: 2vw;
    }
    input[type=date]{
        display: inline;
        max-width: 20%;
    }
    .date-group{
        text-align:center;
        margin-top: 5%;
    }
</style>


<div class="container email-container">

    <form action="/car/mail/send?mail=true" method="post">

        <div class="form-group">
            <label>To:</label>
            <input required id="to" type="email" class="form-control" name="to" value="<?php print $defaultEmail; ?>" aria-describedby="emailHelp" placeholder="To"...>
        </div>
        <div class="form-group">
            <label>From:</label>
            <input required type="email" class="form-control" name="from" aria-describedby="emailHelp" placeholder="From"...>
        </div>
        <div class="form-group">
            <label>Email Subject</label>
            <input type="text" class="form-control" name="subject" value="<?php print $defaultSubject; ?>" placeholder="Enter email subject line...">
        </div>
        <div class="form-group date-group">
            <h5>Case Reviews Date Range</h5>
            <label>From: </label>
            <input required type="date" id="startDate" name="startDate" class="form-control">

            <label>To: </label>
            <input required type="date" name="endDate" class="form-control" value="<?php print $defaultPickerDate; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Send Mail</button>

    </form>

</div>