<?php
/**
 *qdPM
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@qdPM.net so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade qdPM to newer
 * versions in the future. If you wish to customize qdPM for your
 * needs please refer to http://www.qdPM.net for more information.
 *
 * @copyright  Copyright (c) 2009  Sergey Kharchishin and Kym Romanets (http://www.qdpm.net)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<?php if ($sf_request->hasParameter('projects_id')) include_component('projects', 'shortInfo', array('projects' => $projects)) ?>


<!--<h1 class="page-title">--><?php //echo __('Kanban Board') ?><!--</h1>-->

<div><?php include_component('kanbanBoard', 'filtersPreview') ?></div>

<?php
echo javascript_include_tag('/js/jquery-1.11.1.min.js');
echo javascript_include_tag('/js/jqwidgets/jqxcore.js');
echo javascript_include_tag('/js/jqwidgets/jqxsortable.js');
echo javascript_include_tag('/js/jqwidgets/jqxkanban.js');
echo javascript_include_tag('/js/jqwidgets/jqxdata.js');

echo stylesheet_tag('/js/jqwidgets/styles/jqx.base.css');


$pOpen = 0;
if ($sf_request->getParameter('projects_id') > 0) {
    $pOpen = 1;
} elseif (isset($filter_by['Projects'])) {
    if (count(explode(',', $filter_by['Projects'])) == 1) {
        $pOpen = 1;
    }
}

?>

<div id="page_width"></div>
<div id="kanbanBoard"></div>

<?php
$state_trans = array("new", "wait", "inprogress", "evaluate", "done");
$state_count = array(0, 0, 0, 0, 0);
$tasks_label = Doctrine_Core::getTable('TasksLabels')->createQuery('t')
    ->orderBy('t.id')
    ->execute();
?>
<script type="text/javascript">
    $(document).ready(function () {
        var fields = [
            {name: "id", type: "string"},
            {name: "status", map: "state", type: "string"},
            {name: "text", map: "label", type: "string"},
            {name: "tags", type: "string"},
            {name: "color", map: "hex", type: "string"},
            {name: "resourceId", type: "number"}
        ];
        var source =
        {
            localData: [
                <?php
                $taskProjectId = 0;
                $parentItemId = 0;
                $counter = 1;
                foreach ($tasks_list as $tasks) {

                    if ($tasks['projects_id'] != $taskProjectId) {
                        $taskProjectId = $tasks['projects_id'];
//        echo "g.AddTaskItem(new JSGantt.TaskItem(" . $counter . ", '" . addslashes($tasks['Projects']['name']) . "','','','ffe763', '" . url_for('ganttChart/index?projects_id=' . $tasks['projects_id']) . "','','',0,1,0," . $pOpen . ",'','')); \n";
                        $parentItemId = $counter;
//        $counter++;
                    }

                    $estimated_time = $tasks['estimated_time'];

                    if ($estimated_time > 0) {
                        $estimated_title = $estimated_time . ' ' . t::__('hours');
                    } else {
                        $estimated_title = '';
                    }

                    $start_date = app::ganttDateFormat($tasks['start_date']);
                    $end_date = app::ganttDateFormat($tasks['due_date']);

                    $level_padding = '';
                    if (count($tasks_tree) > 0) {
                        if ($tasks_tree[$tasks['id']]['level'] > 0) {
                            $level_padding = str_repeat('&nbsp;-&nbsp;', $tasks_tree[$tasks['id']]['level']);
                        }
                    }
                    $ass_array = explode(',', $tasks['assigned_to']);
//    echo "<br />" . $counter . ", '" . $level_padding . addslashes($tasks['name']) . ",<br />    start:" . $start_date . ", end:" . $end_date . "<br />   URL:" . url_for('tasksComments/index?tasks_id=' . $tasks['id'] . '&projects_id=' . $tasks['projects_id']) . "', status id:" . $tasks['tasks_status_id'] . " " . ($tasks['tasks_status_id'] > 0 ? addslashes($tasks['TasksStatus']['name']) : '') . "',<br />progress: " . (int)$tasks['progress'] . "% <br />," . $parentItemId . ",'','','" . url_for('tasks/info?id=' . $tasks['id'] . '&projects_id=' . $tasks['projects_id']) . "')); \n";
                    echo "{id: \"" . $tasks['id'] . "\", state: \"" . $state_trans[$tasks['tasks_status_id'] - 1] . "\", label: \"" . $tasks['name'] . "\", tags: \"".$tasks_label[$tasks['tasks_label_id']-1]."\", hex: \"#5dc3f0\", resourceId: ".$ass_array[0]."},";
                    $state_count[$tasks['tasks_status_id']-1]++;
                    $counter++;
                }
                ?>
            ],
            dataType: "array",
            dataFields: fields
        };
//        var divWidth = document.getElementById("bd").clientWidth;
        var dataAdapter = new $.jqx.dataAdapter(source);
        var resourcesAdapterFunc = function () {
            var resourcesSource =
            {
                localData: [
                    {
                        id: 0,
                        name: "Unknown",
                        image: "<?php echo image_path('kanbanBoard/common.png'); ?>",
                        common: true
                    },
                    {id: 3, name: "admin", image: "<?php echo "http://localhost/www/uploads/users/841661-629c1f14015191.5627be627c443.png"; ?>"},
                    {id: 5, name: "developer", image: "<?php echo "http://localhost/www/uploads/users/850070-pic.PNG"; ?>"},
                    {id: 4, name: "manager", image: "<?php echo "http://localhost/www/uploads/users/145399-pic2.PNG"; ?>"},
                ],
                dataType: "array",
                dataFields: [
                    {name: "id", type: "number"},
                    {name: "name", type: "string"},
                    {name: "image", type: "string"},
                    {name: "common", type: "boolean"}
                ]
            };
            var resourcesDataAdapter = new $.jqx.dataAdapter(resourcesSource);
            return resourcesDataAdapter;
        }
        $('#kanbanBoard').jqxKanban({
            height:400,
            resources: resourcesAdapterFunc(),
            source: dataAdapter,
            columns: [
                {text: <?php echo "\"New (".$state_count[0].")\"";?>, dataField: "new"},
                {text: <?php echo "\"Waiting (".$state_count[1].")\"";?>, dataField: "wait"},
                {text: <?php echo "\"In Progress (".$state_count[2].")\"";?>, dataField: "inprogress"},
                {text: <?php echo "\"Evaluation (".$state_count[3].")\"";?>, dataField: "evaluate"},
                {text: <?php echo "\"Done (".$state_count[4].")\"";?>, dataField: "done"}
            ]
        });
    });
</script>