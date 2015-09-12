<?php

return sprintf(
    '%s (%s)',
    substr(`git rev-parse HEAD`, 0, 7),
    date('Y-m-d H:i:s')
);
