<?php

namespace app\enums;

abstract class VideoStatus
{
    const NO_ACTION = 0;
    const NEED_CONVERT = 1;
    const CONVERTING = 2;
    const CONVERTING_ERROR = 3;
}