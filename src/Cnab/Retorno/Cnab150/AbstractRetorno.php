<?php
namespace Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab150;

use Eduardokum\LaravelBoleto\Util;
use \Eduardokum\LaravelBoleto\Cnab\Retorno\AbstractRetorno as AbstractRetornoGeneric;
use Eduardokum\LaravelBoleto\Contracts\Cnab\Retorno\Cnab150\Header as HeaderContract;
use Eduardokum\LaravelBoleto\Contracts\Cnab\Retorno\Cnab150\Detalhe as DetalheContract;
use Eduardokum\LaravelBoleto\Contracts\Cnab\Retorno\Cnab150\Trailer as TrailerContract;
use Illuminate\Support\Collection;

/**
 * Class AbstractRetorno
 *
 * @method  \Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab150\Detalhe getDetalhe($i)
 * @method  \Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab150\Header getHeader()
 * @method  \Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab150\Trailer getTrailer()
 * @method  \Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab150\Detalhe detalheAtual()
 * @package Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab150
 */
abstract class AbstractRetorno extends AbstractRetornoGeneric
{
    /**
     * @param String $file
     * @throws \Exception
     */
    public function __construct($file)
    {
        parent::__construct($file);

        $this->header = new Header();
        $this->trailer = new Trailer();
    }

    /**
     * @param array $header
     *
     * @return boolean
     */
    abstract protected function processarHeader(array $header);

    /**
     * @param array $detalhe
     *
     * @return boolean
     */
    abstract protected function processarDetalhe(array $detalhe);

    /**
     * @param array $trailer
     *
     * @return boolean
     */
    abstract protected function processarTrailer(array $trailer);

    /**
     * Incrementa o detalhe.
     */
    protected function incrementDetalhe()
    {
        $this->increment++;
        $this->detalhe[$this->increment] = new Detalhe();
    }

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
            $inicio = $this->rem(1, 1, $linha);

            if ($inicio == 'A') {
                $this->processarHeader($linha);
            } elseif ($inicio == 'Z') {
                $this->processarTrailer($linha);
            } else {
                $this->incrementDetalhe();
                if ($this->processarDetalhe($linha) === false) {
                    unset($this->detalhe[$this->increment]);
                    $this->increment--;
                }
            }
        }
        if (method_exists($this, 'finalize')) {
            call_user_func([$this, 'finalize']);
        }

        return $this->setProcessado();
    }

    /**
     * Retorna o array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [
            'header' => $this->header->toArray(),
            'trailer' => $this->trailer->toArray(),
            'detalhes' => new Collection()
        ];
        foreach ($this->detalhe as $detalhe) {
            $array['detalhes']->push($detalhe->toArray());
        }
        return $array;
    }
}
