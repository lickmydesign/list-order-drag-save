<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 01/03/2019
 * Time: 19:50
 *
 * Note: this was adapted from David Walsh's script: https://davidwalsh.name/mootools-drag-ajax
 * I've disabled the autoSubmit checkbox, as I want this as the default behaviour
 */

if (isset($_POST['do_submit']))  {
	/* split the value of the sortation */
	$ids = explode(',',$_POST['sort_order']);
	/* run the update query for each id */
	$output = "";
	foreach($ids as $index => $id) {
		$id = (int) $id;
		if ($id != '') {
			$query = 'UPDATE test_table SET sort_order = '.($index + 1).' WHERE id = '.$id;
			$output .= $query . "<br /> ";
		}
	}

	echo "<p>Would have run: </p>" . $output;

	/* now what? */
	if ($_POST['byajax']) {
	    die();
	} else {
	    $message = 'Sortation has been saved.';
	}
}

// would get data from db here normally...
$people = array(
	0 => array(
		'id' => 1,
		'name' => 'Merl',
		'display_order' => 3
	),
	1 => array(
		'id' => 2,
		'name' => 'Mimi',
		'display_order' => 1
	),
	2 => array(
		'id' => 3,
		'name' => 'Adam',
		'display_order' => 2
	),
	3 => array(
		'id' => 4,
		'name' => 'Stanley',
		'display_order' => 4
	)
);
?>
<html>
<head>
	<title>List Order Drag and Save Test</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<style type="text/css">
        body {
            padding-top: 2em;
        }
        #sortable-list li	{
            background-color: #ddd;
            border: 1px solid #999;
            cursor: move;
            list-style: none;
            margin: 1em 0;
            padding: 0.5em 0.8em;
            width: 30em;
        }
	</style>
</head>
<body>

    <header class="container">
        <h1>List Order Drag and Save Test</h1>
    </header>

    <main class="container">

        <p class="text-muted">See: <a href="https://davidwalsh.name/mootools-drag-ajax">https://davidwalsh.name/mootools-drag-ajax</a></p>

        <div id="message-box" class="alert alert-warning"><?php echo isset($message) ? $message : ""; ?> Waiting for sortation submission...</div>

        <p>Drag and drop the elements below:</p>

        <?php
//		$auto_submit_checked = "";
//		if (isset($_POST['autoSubmit']) && $_POST['autoSubmit']) {
//		    $auto_submit_checked = 'checked="checked"';
//		}
        ?>
        <form id="dd-form" action="" method="">
            <!--p>
                <input type="checkbox" value="1" name="autoSubmit" id="autoSubmit" <?php //echo $auto_submit_checked; ?> />
                <label for="autoSubmit">Automatically submit on drop event</label>
            </p-->
            <?php
            echo "<ul id='sortable-list'>";
            foreach($people as $i => $row) {
                echo "<li data-id='".$row['id']."'>id:" . $row['id'] . ' | name: <strong>' . $row['name'] . '</strong> (' . $row['display_order'] .")</li>";
                $order[] = $row['id'];
            }
            echo "</ul>";
            ?>
            <input type="hidden" name="sort_order" id="sort_order" value="<?php echo implode(',',$order);?>" />
            <button type="submit" name="submit_butt" class="btn btn-primary" class="btn btn-primary">Submit Sortation</button>
        </form>
    </main>

    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script type="text/javascript">

        /* when the DOM is ready */
        $(function() {
            /* grab important elements */
            var sortInput = $('#sort_order');
            var autoSubmit = $('#autoSubmit');
            var messageBox = $('#message-box');
            var list = $('#sortable-list');
            /* create requesting function to avoid duplicate code */
            var request = function() {
                $.ajax({
                    beforeSend: function() {
                        messageBox.text('Updating the sort order in the database.');
                    },
                    complete: function() {
                        messageBox.text('Database has been updated.');
                    },
                    data: 'sort_order=' + sortInput[0].value + '&do_submit=1&byajax=1',
                    type: 'post',
                    url: '<?php echo $_SERVER["REQUEST_URI"]; ?>'
                });
            };
            /* worker function */
            var fnSubmit = function(save) {
                var sortOrder = [];
                list.children('li').each(function(){
                    sortOrder.push($(this).data('id'));
                });
                sortInput.val(sortOrder.join(','));
                // console.log(sortInput.val());
                if (save) {
                    request();
                }
            };
            /* store values */
            list.children('li').each(function() {
                var li = $(this);
                li.data('id',li.attr('data-id'));
            });
            /* sortables */
            list.sortable({
                opacity: 0.7,
                update: function() {
                    fnSubmit(1); //hard coded, to re-enable the checkbox, use autoSubmit[0].checked
                }
            });
            list.disableSelection();
            /* ajax form submission */
            $('#dd-form').bind('submit',function(e) {
                if(e) e.preventDefault();
                fnSubmit(true);
            });
        });

    </script>
</body>
</html>
