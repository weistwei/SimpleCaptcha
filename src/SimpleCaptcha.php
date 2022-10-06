<?php

namespace NickLabs\SimpleCaptcha;

use Exception;
use NickLabs\SimpleCanvas\SimpleCanvas;
use NickLabs\SimpleCanvas\SimpleCanvasUtils;

class SimpleCaptcha {

    /** @var int $width */
    private $width = 60;

    /** @var int $height */
    private $height = 25;

    /** @var string $font */
    private $font;

    /** @var int $fontSizeMax */
    private $fontSizeMax = 20;

    /** @var int $fontSizeMin */
    private $fontSizeMin = 10;

    /** @var int $dotNoiseNum */
    private $dotNoiseNum = 0;

    /** @var int $lineNoiseNum */
    private $lineNoiseNum = 0;

    /** @var bool $gaussianNoise */
    private $gaussianNoise = false;

    /** @var bool $enableChar */
    private $enableChar = true;

    /** @var string $chars */
    private $validationChars = 'ABCDEFGHJKLMNPQRSTUVWXYZ';

    /** @var bool $enableNumber */
    private $enableNumber = true;

    /** @var string $numbers */
    private $validationNumbers = '123456789';

    public function __construct(){

    }

    /**
     * @param int $width
     * @return $this
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-06
     */
    public function setWidth(int $width): SimpleCaptcha{
        $this->width = $width;
        return $this;
    }

    /**
     * @param int $height
     * @return $this
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-06
     */
    public function setHeight(int $height): SimpleCaptcha{
        $this->height = $height;
        return $this;
    }

    /**
     * @param string $fontPath
     * @return $this
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-06
     */
    public function setFont(string $fontPath): SimpleCaptcha{
        if(is_readable($fontPath)){
            $this->font = $fontPath;
        }
        return $this;
    }

    /**
     * @param int $min
     * @param int $max
     * @return $this
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-07
     */
    public function setFontSize(int $min, int $max): SimpleCaptcha{
        $this->fontSizeMin = $min;
        $this->fontSizeMax = $max;
        return $this;
    }

    /**
     * @param int $num
     * @return $this
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-06
     */
    public function setDotNoiseNum(int $num): SimpleCaptcha{
        $this->dotNoiseNum = $num;
        return $this;
    }

    /**
     * @param int $num
     * @return $this
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-06
     */
    public function setLineNoiseNum(int $num): SimpleCaptcha{
        $this->lineNoiseNum = $num;
        return $this;
    }

    /**
     * @param bool $status
     * @return $this
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-06
     */
    public function setGaussianNoise(bool $status = true): SimpleCaptcha{
        $this->gaussianNoise = $status;
        return $this;
    }

    /**
     * @param string $chars
     * @return $this
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-06
     */
    public function setValidationChars(string $chars): SimpleCaptcha{
        $this->validationChars = $chars;
        return $this;
    }

    /**
     * @param string $chars
     * @return $this
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-06
     */
    public function setValidationNumbers(string $chars): SimpleCaptcha{
        $this->validationNumbers = $chars;
        return $this;
    }

    /**
     * @param bool $status
     * @return $this
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-06
     */
    public function enableChar(bool $status): SimpleCaptcha{
        $this->enableChar = $status;
        return $this;
    }

    /**
     * @param bool $status
     * @return $this
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-06
     */
    public function enableNumber(bool $status): SimpleCaptcha{
        $this->enableNumber = $status;
        return $this;
    }

    public function createWithString(){

    }

    /**
     * @param int $length
     * @return array
     * @throws Exception
     * @author Nick <weist.wei@gmail.com>
     * @date 2022-10-06
     */
    public function generatorCaptcha(int $length = 5): array{
        $length = ($length <= 0) ? 5 : $length;
        $code = '';
        $canvas = SimpleCanvas::createFromWidthAndHeight($this->width, $this->height);
        $canvas->setBackground('000000');
        $canvas->setFont($this->font);

        if($this->dotNoiseNum > 0){
            for($i = 0; $i < $this->dotNoiseNum; $i++){
                $randomX = rand(0, $this->width);
                $randomY = rand(0, $this->height);
                $randomColor = SimpleCanvasUtils::randomHexColor();
                $canvas->drawPixel($randomX, $randomY, $randomColor);
            }
        }

        if($this->lineNoiseNum > 0){
            for($i = 0; $i < $this->lineNoiseNum; $i++){
                $randomStartX = rand(0, $this->width);
                $randomStartY = rand(0, $this->height);
                $randomEndX = rand(0, $this->width);
                $randomEndY = rand(0, $this->height);
                $randomColor = SimpleCanvasUtils::randomHexColor();
                $canvas->drawLine($randomStartX, $randomStartY, $randomEndX, $randomEndY, $randomColor);
            }
        }

        if($length > 0){
            $char = '';
            if($this->enableChar){
                $char = $char . $this->validationChars;
            }
            if($this->enableNumber){
                $char = $char . $this->validationNumbers;
            }
            $avgWidth = $this->width / $length;
            for($i = 0; $i < $length; $i++){
                $code .= $randomChar = $char[rand(0, (strlen($char) - 1))];

                $randomFontSize = rand($this->fontSizeMin, $this->fontSizeMax);
                $positionX = ($i * $avgWidth) + 5;
                $positionY = rand(0, ($this->height - $randomFontSize));
                $randomColor = SimpleCanvasUtils::randomHexColor();

                $randomAngle = rand(0, 50);

                $canvas->writeText($randomChar, $randomFontSize, $positionX, $positionY, $randomColor, $randomAngle);
            }
        }

        if($this->gaussianNoise){
            $canvas->filterGaussianBlur();
        }

        return [
            'base64String' => "data:image/png;base64,{$canvas->outputPngBase64String()}",
            'code' => $code
        ];
    }
}