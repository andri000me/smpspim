

            <!-- Footer-->
            <footer class="footer">
                <span class="pull-right">
                    <?php echo $this->pengaturan->getNamaApp(); ?>
                </span>
                2017<?php if(date('Y') > 2017) echo '-'.date('Y'); ?> Copyright <strong><?php echo $this->pengaturan->getNamaLembagaSingk(); ?> - <?php echo $this->pengaturan->getNamaLembaga(); ?></strong>
            </footer>

        </div>
    </body>
</html>