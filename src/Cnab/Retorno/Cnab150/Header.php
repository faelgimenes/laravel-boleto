<?php
namespace Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab150;

use Carbon\Carbon;
use Eduardokum\LaravelBoleto\Contracts\Cnab\Retorno\Cnab150\Header as HeaderContract;
use Eduardokum\LaravelBoleto\MagicTrait;

class Header implements HeaderContract
{
    use MagicTrait;

    /**
     * @var string
     */
    protected $codigoRegistro;
    /**
     * @var string
     */
    protected $codigoRemessa;
    /**
     * @var string
     */
    protected $codigoConvenio;
    /**
     * @var string
     */
    protected $nomeEmpresa;
    /**
     * @var string
     */
    protected $codigoBanco;
    /**
     * @var string
     */
    protected $nomeBanco;
    /**
     * @var string
     */
    protected $dataGeracao;
    /**
     * @var string
     */
    protected $versaoLayout;
    /**
     * @var string
     */
    protected $identificacaoServico;


    /**
     * @param string $codigoRegistro
     *
     * @return Header
     */
    public function setCodigoRegistro($codigoRegistro)
    {
        $this->codigoRegistro = $codigoRegistro;

        return $this;
    }

    /**
     * @return string
     */
    public function getCodigoRegistro()
    {
        return $this->codigoRegistro;
    }

    /**
     * @param string $codigoRemessa
     *
     * @return Header
     */
    public function setCodigoRemessa($codigoRemessa)
    {
        $this->codigoRemessa = $codigoRemessa;

        return $this;
    }

    /**
     * @return string
     */
    public function getCodigoRemessa()
    {
        return $this->codigoRemessa;
    }

    /**
     * @param string $codigoConvenio
     *
     * @return Header
     */
    public function setCodigoConvenio($codigoConvenio)
    {
        $this->codigoConvenio = ltrim(trim($codigoConvenio, ' '), '0');

        return $this;
    }

    /**
     * @return string
     */
    public function getCodigoConvenio()
    {
        return $this->codigoConvenio;
    }

    /**
     * @param string $nomeEmpresa
     *
     * @return Header
     */
    public function setNomeEmpresa($nomeEmpresa)
    {
        $this->nomeEmpresa = $nomeEmpresa;

        return $this;
    }

    /**
     * @return string
     */
    public function getNomeEmpresa()
    {
        return $this->nomeEmpresa;
    }

    /**
     * @param string $codigoBanco
     *
     * @return Header
     */
    public function setCodigoBanco($codigoBanco)
    {
        $this->codigoBanco = $codigoBanco;

        return $this;
    }

    /**
     * @return string
     */
    public function getCodigoBanco()
    {
        return $this->codigoBanco;
    }

    /**
     * @param string $nomeBanco
     *
     * @return Header
     */
    public function setNomeBanco($nomeBanco)
    {
        $this->nomeBanco = $nomeBanco;

        return $this;
    }

    /**
     * @return string
     */
    public function getNomeBanco()
    {
        return $this->nomeBanco;
    }

    /**
     * @param string $dataGeracao
     *
     * @return Header
     */
    public function setDataGeracao($data, $format = 'dmy')
    {
        $this->dataGeracao = trim($data, '0 ') ? Carbon::createFromFormat($format, $data) : null;

        return $this;
    }

    /**
     * @return string
     */
    public function getDataGeracao($format = 'd/m/Y')
    {
        return $this->dataGeracao instanceof Carbon
            ? ($format === false ? $this->dataGeracao : $this->dataGeracao->format($format))
            : null;
    }

    /**
     * @param string $versaoLayout
     *
     * @return Header
     */
    public function setVersaoLayout($versaoLayout)
    {
        $this->versaoLayout = $versaoLayout;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersaoLayout()
    {
        return $this->versaoLayout;
    }

    /**
     * @param string $identificacaoServico
     *
     * @return Header
     */
    public function setIdentificacaoServico($identificacaoServico)
    {
        $this->identificacaoServico = $identificacaoServico;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentificacaoServico()
    {
        return $this->identificacaoServico;
    }
}
