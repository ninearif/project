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
<?php if($sf_request->hasParameter('projects_id')) include_component('projects','shortInfo', array('projects'=>$projects)) ?>


<h1 class="page-title"><?php echo __('Kanban Board') ?></h1>

<div><?php  include_component('ganttChart','filtersPreview') ?></div>

<?php
echo javascript_include_tag('/js/jquery-1.11.1.min.js');
echo javascript_include_tag('/js/jqwidgets/jqxcore.js');
echo javascript_include_tag('/js/jqwidgets/jqxsortable.js');
echo javascript_include_tag('/js/jqwidgets/jqxkanban.js');
echo javascript_include_tag('/js/jqwidgets/jqxdata.js');

echo stylesheet_tag('/js/jqwidgets/styles/jqx.base.css');


$pOpen = 0;
if($sf_request->getParameter('projects_id')>0)
{
    $pOpen = 1;
}
elseif(isset($filter_by['Projects']))
{
    if(count(explode(',',$filter_by['Projects']))==1)
    {
        $pOpen = 1;
    }
}

?>

<div id="page_width"></div>
<div id="kanbanBoard"></div>

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
                {id: "1161", state: "new", label: "Annual report", tags: "document", hex: "#5dc3f0", resourceId: 3},
                {id: "1645", state: "work", label: "TOR project", tags: "document", hex: "#5dc3f0", resourceId: 1},
                {
                    id: "9213",
                    state: "new",
                    label: "Website installation",
                    tags: "developer",
                    hex: "#6bbd49",
                    resourceId: 3
                },
                {
                    id: "6546",
                    state: "done",
                    label: "Edit Meeting Schedule",
                    tags: "schedule",
                    hex: "#f19b60",
                    resourceId: 4
                },
                {id: "1163", state: "wait", label: "New Document", tags: "document", hex: "#5dc3f0", resourceId: 3},
                {id: "1622", state: "work", label: "Meeting doc", tags: "document", hex: "#5dc3f0", resourceId: 1},
                {
                    id: "9200",
                    state: "work",
                    label: "Fix bug#132",
                    tags: "developer,bug",
                    hex: "#6bbd49",
                    resourceId: 3
                },
                {id: "9034", state: "evaluate", label: "Login 404 issue", tags: "developer,issue", hex: "#6bbd49"}
            ],
            dataType: "array",
            dataFields: fields
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        var resourcesAdapterFunc = function () {
            var resourcesSource =
            {
                localData: [
                    {id: 0, name: "Unknown", image: "<?php echo image_path('kanbanBoard/common.png'); ?>", common: true},
                    {id: 1, name: "Andrew Fuller", image: "<?php echo image_path('kanbanBoard/andrew.png'); ?>"},
                    {id: 2, name: "Janet Leverling", image: "<?php echo image_path('kanbanBoard/janet.png'); ?>"},
                    {id: 3, name: "Steven Buchanan", image: "<?php echo image_path('kanbanBoard/steven.png'); ?>"},
                    {id: 4, name: "Nancy Davolio", image: "<?php echo image_path('kanbanBoard/nancy.png'); ?>"},
                    {id: 5, name: "Michael Buchanan", image: "<?php echo image_path('kanbanBoard/Michael.png'); ?>"},
                    {id: 6, name: "Margaret Buchanan", image: "<?php echo image_path('kanbanBoard/margaret.png'); ?>"},
                    {id: 7, name: "Robert Buchanan", image: "<?php echo image_path('kanbanBoard/robert.png'); ?>"},
                    {id: 8, name: "Laura Buchanan", image: "<?php echo image_path('kanbanBoard/Laura.png'); ?>"},
                    {id: 9, name: "Laura Buchanan", image: "<?php echo image_path('kanbanBoard/Anne.png'); ?>"}
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
            resources: resourcesAdapterFunc(),
            source: dataAdapter,
            columns: [
                {text: "+ New (2)", dataField: "new"},
                {text: "Waiting (1/1)", dataField: "wait"},
                {text: "In Progress (3/3)", dataField: "work"},
                {text: "Evaluation (1/2)", dataField: "evaluate"},
                {text: "Done (1)", dataField: "done"}
            ]
        });
    });
</script>