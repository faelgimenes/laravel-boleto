<?php
namespace Eduardokum\LaravelBoleto\Cnab\Retorno\Dai;

use Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab240\AbstractRetorno as AbstractRetornoBase;

/**
 * Class AbstractRetorno
 *
 * @method  \Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab240\Detalhe getDetalhe($i)
 * @method  \Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab240\Header getHeader()
 * @method  \Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab240\Trailer getTrailer()
 * @method  \Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab240\Detalhe detalheAtual()
 * @package Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab240
 */
abstract class AbstractRetorno extends AbstractRetornoBase
{
    /**
     * Processa o arquivo
     *
     * @return $this
     * @throws \Exception
     */
    public function processar()
    {
        if ($this->isProcessado()) {
            return $this;
        }

        if (method_exists($this, 'init')) {
            call_user_func([$this, 'init']);
        }

        foreach ($this->file as $linha) {
            $recordType = $this->rem(8, 8, $linha);

            if ($recordType == '0') {
                $this->processarHeader($linha);
            } elseif ($recordType == '1') {
                $this->processarHeaderLote($linha);
            } elseif ($recordType == '3') {
                if ($this->getSegmentType($linha) == 'A') {
                    $this->incrementDetalhe();
                }

                if ($this->processarDetalhe($linha) === false) {
                    unset($this->detalhe[$this->increment]);
                    $this->increment--;
                }
            } elseif ($recordType == '5') {
                $this->processarTrailerLote($linha);
            } elseif ($recordType == '9') {
                $this->processarTrailer($linha);
            }
        }

        if (method_exists($this, 'finalize')) {
            call_user_func([$this, 'finalize']);
        }

        return $this->setProcessado();
    }
}
