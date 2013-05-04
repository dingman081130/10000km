<?php
/**
 * @param attry $create_trips 我创建的trips
 * @param attry $join_trips 我参加的trips
 */
?>
<div class="row">
    <div class="span2">
        <?php
        $this->widget('HomeSidebarWidget', array(
            'tab' => 'trip',
        ));
        ?>
    </div>

    <div class="span10">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#create" data-toggle="tab">我发布的旅行</a></li>
            <li><a href="#join" data-toggle="tab">我参与的旅行</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="create">
                <ul class="unstyled trip-list">
                    <?php
                    foreach ($create_trips as $trip) {
                        $this->renderView(array('trip', 'tripItem'), array('trip' => $trip));
                    }
                    ?>
                </ul>
            </div>
            
            <div class="tab-pane" id="join">
                <ul class="unstyled trip-list">
                    <?php
                    foreach ($join_trips as $trip) {
                        $this->renderView(array('trip', 'tripItem'), array('trip' => $trip));
                    }
                    ?>
                </ul>
            </div>
        </div>

    </div>

</div>


