
<?php
use \app\helpers\Tools;

foreach ($articles as $a) { ?>
    <article>
        <div class="left">
            <a href="<?= $a->url ?>"><img src="<?= $a->image ? $a->image->url : '' ?>"></a>
        </div>
        <div class="right">
            <a href="<?= $a->url ?>">
                <h4><?= Tools::string($a->title, 33) ?></h4>
                <span class="small pull-left eye pc-hide"><i class="fa fa-leaf leaf "></i>
                    <?php
                    if ($a->keywords) {
                        $n = strpos($a->keywords, ',');
                        echo $n ? substr($a->keywords, 0, $n) : $a->keywords;
                    }
                    ?>
                            </span>
                <span class="small pull-right clock pc-hide"><i
                        class="fa fa-eye eye"></i>&nbsp;&nbsp;<?= $a->read_count ?></span>
            </a>
            <p class="summary"><?= Tools::string($a->summary, 160) ?></p>
            <p class="phone-hide">
                <i class="fa fa-leaf leaf"></i>
                <?php
                if ($a->keywords) {
                    $n = strpos($a->keywords, ',');
                    echo $n ? substr($a->keywords, 0, $n) : $a->keywords;
                }
                ?>&nbsp;&nbsp;&nbsp;
                <i class="fa fa-clock-o clock"></i> <?= date('Y-m-d', $a->created_at) ?>&nbsp;&nbsp;&nbsp;
                <i class="fa fa-comment-o comment"></i> 评论（ <?= $is_first_page ? '<span id="sourceId::article'.$a->id.'"
                                                                  class="cy_cmt_count"></span>
                <script id="cy_cmt_num"
                        src="http://changyan.sohu.com/upload/plugins/plugins.list.count.js?clientId=cysBHKkOH">
                </script>' : 0?>
                ）&nbsp;&nbsp;&nbsp;
                <i class="fa fa-eye eye"></i>浏览（<?= $a->read_count ?>）&nbsp;&nbsp;&nbsp;
                <a class="pull-right" href="<?= $a->url ?>">阅读原文>></a>
            </p>
        </div>
    </article>
<?php } ?>