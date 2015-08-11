<?php

namespace app\models;

class VideoStatus
{
    const NO_ACTION = 0;
    const NEED_CONVERT = 1;
    const CONVERTING = 2;
    const CONVERSION_ERROR = 3;

}