<?php

namespace Eduardokum\LaravelBoleto\Contracts\Cnab;

use Illuminate\Support\Collection;

interface RetornoCnab150 extends Retorno
{
    /**
     * @return mixed
     */
    public function getCodigoBanco();

    /**
     * @return mixed
     */
    public function getBancoNome();

    /**
     * @return Collection
     */
    public function getDetalhes();

    /**
     * @return Retorno\Detalhe
     */
    public function getDetalhe($i);

    /**
     * @return Retorno\Cnab150\Header
     */
    public function getHeader();

    /**
     *  @return Retorno\Cnab150\Trailer
     */
    public function getTrailer();

    /**
     * @return string
     */
    public function processar();

    /**
     * @return array
     */
    public function toArray();
}
