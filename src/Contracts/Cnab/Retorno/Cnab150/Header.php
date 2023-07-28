<?php
namespace Eduardokum\LaravelBoleto\Contracts\Cnab\Retorno\Cnab150;

interface Header
{
    /**
     * @return mixed
     */
    public function getCodigoRegistro();

    /**
     * @return mixed
     */
    public function getCodigoRemessa();

    /**
     * @return mixed
     */
    public function getCodigoConvenio();

    /**
     * @return mixed
     */
    public function getNomeEmpresa();

    /**
     * @return mixed
     */
    public function getCodigoBanco();

    /**
     * @return mixed
     */
    public function getNomeBanco();

    /**
     * @param string $format
     *
     * @return \Carbon\Carbon
     */
    public function getDataGeracao($format = 'd/m/Y');

    /**
     * @return mixed
     */
    public function getVersaoLayout();

    /**
     * @return mixed
     */
    public function getIdentificacaoServico();

    /**
     * @return array
     */
    public function toArray();
}
