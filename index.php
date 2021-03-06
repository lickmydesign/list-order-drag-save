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
//		$this->db->trans_start();
		if ($id != '') {
			$sort_order = $index+1;
		    //todo: would be nice to get this into one query (in CI use transactions)
			$sql = "UPDATE test_table SET sort_order = $sort_order WHERE id = $id";
//			$this->db->query($sql, array($sort_order, $id));
			$output .= $sql . "<br /> ";
		}
//		$this->db->trans_complete();
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
$people2 = array(
	0 => array(
		'id' => 5,
		'name' => 'Mike',
		'display_order' => 2
	),
	1 => array(
		'id' => 6,
		'name' => 'Jake',
		'display_order' => 1
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
        .sortable-list li	{
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

        <div class="card bg-light">
            <div class="card-body">
                Reference: <a href="https://davidwalsh.name/mootools-drag-ajax">https://davidwalsh.name/mootools-drag-ajax</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">

                <p>Drag and drop the elements below:</p>

                <div id="message-box-1"><?php echo isset($message) ? $message : ""; ?></div>

                <?php
                echo "<ul id='list-1' class='sortable-list' data-messagebox='message-box-1'>";
                foreach($people as $i => $row) {
                    echo "<li data-id='".$row['id']."'>id:" . $row['id'] . ' | name: <strong>' . $row['name'] . '</strong> (original display order: ' . $row['display_order'] .")</li>";
                }
                echo "</ul>";
                ?>

            </div> <!-- of .col-md-6 -->

            <div class="col-md-6">

                <p>Drag and drop the elements below:</p>

                <div id="message-box-2"><?php echo isset($message) ? $message : ""; ?></div>

                <?php
                echo "<ul id='list-2' class='sortable-list' data-messagebox='message-box-2'>";
                foreach($people2 as $i => $row) {
                    echo "<li data-id='".$row['id']."'>id:" . $row['id'] . ' | name: <strong>' . $row['name'] . '</strong> (original display order: ' . $row['display_order'] .")</li>";
                }
                echo "</ul>";
                ?>

            </div> <!-- of .col-md-6 -->

        </div> <!-- of .row -->
    </main>

    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script type="text/javascript">
        $(function() {
            var list = $('.sortable-list');
            /* create requesting function to avoid duplicate code */
            var request = function(sortOrderList, messageBox) {
                var messageElement = $('#'+messageBox);
                $.ajax({
                    beforeSend: function() {
                        messageElement.show();
                        messageElement.removeClass().addClass('alert alert-info mt-3');
                        messageElement.text('Saving...');
                    },
                    complete: function() {
                        messageElement.removeClass().addClass('alert alert-success mt-3');
                        messageElement.text('Sort order saved.');
                        messageElement.delay(3000).fadeOut();
                    },
                    data: 'sort_order=' + sortOrderList + '&do_submit=1&byajax=1',
                    type: 'post',
                    url: '<?php echo $_SERVER["REQUEST_URI"]; ?>'
                });
            };
            /* worker function */
            var fnSubmit = function(messageBox, currentList) {
                /* collect list order */
                var sortOrder = [];
                $('#'+currentList).children('li').each(function(){
                    sortOrder.push($(this).data('id'));
                });
                request(sortOrder, messageBox);
            };
            /* store values */
            list.children('li').each(function() {
                var li = $(this);
                li.data('id', li.attr('data-id'));
            });
            list.sortable({
                opacity: 0.7,
                update: function(event, ui) {
                    var messageBox = $(this).data('messagebox');
                    var currentList = this.id;
                    fnSubmit(messageBox, currentList);
                }
            });
            list.disableSelection();
        });
    </script>
</body>
</html>
