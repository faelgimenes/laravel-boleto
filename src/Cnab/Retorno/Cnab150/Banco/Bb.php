<?php
namespace Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab150\Banco;

use Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab150\AbstractRetorno;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Contracts\Cnab\RetornoCnab150;
use Eduardokum\LaravelBoleto\Util;
use Illuminate\Support\Arr;

class Bb extends AbstractRetorno implements RetornoCnab150
{
    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_BB;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '00' => 'Débito efetuado',
        '01' => 'Débito não efetuado - Insuficiência de fundos',
        '02' => 'Débito não efetuado - Conta corrente não cadastrada “04” = Débito não efetuado - Outras restrições',
        '05' => 'Débito não efetuado – valor do débito excede valor limite aprovado.',
        '10' => 'Débito não efetuado - Agência em regime de encerramento',
        '12' => 'Débito não efetuado - Valor inválido',
        '13' => 'Débito não efetuado - Data de lançamento inválida',
        '14' => 'Débito não efetuado - Agência inválida',
        '15' => 'Débito não efetuado - conta corrente inválida',
        '18' => 'Débito não efetuado - Data do débito anterior à do processamento',
        '30' => 'Débito não efetuado - Sem contrato de débito automático',
        '31' => 'Débito efetuado em data diferente da data informada – feriado na praça de débito',
        '96' => 'Manutenção do Cadastro',
        '97' => 'Cancelamento - Não encontrado',
        '98' => 'Cancelamento - Não efetuado, fora do tempo hábil',
        '99' => 'Cancelamento - cancelado conforme solicitação',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
    ];

    /**
     * Roda antes dos metodos de processar
     */
    protected function init()
    {
        $this->totais = [
            'liquidados' => 0,
            'entradas' => 0,
            'baixados' => 0,
            'protestados' => 0,
            'erros' => 0,
            'alterados' => 0,
            'outros' => 0,
        ];
    }

    /**
     * @param array $header
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarHeader(array $header)
    {
        $this->getHeader()
            ->setCodigoRegistro($this->rem(1, 1, $header))
            ->setCodigoRemessa($this->rem(2, 2, $header))
            ->setCodigoConvenio($this->rem(3, 22, $header))
            ->setNomeEmpresa($this->rem(23, 42, $header))
            ->setCodigoBanco($this->rem(43, 45, $header))
            ->setNomeBanco($this->rem(46, 65, $header))
            ->setDataGeracao($this->rem(66, 73, $header), 'Ymd')
            ->setVersaoLayout($this->rem(80, 81, $header))
            ->setIdentificacaoServico($this->rem(82, 98, $header))
        ;

        return true;
    }

    /**
     * @param array $detalhe
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarDetalhe(array $detalhe)
    {
        if ($this->rem(1, 1, $detalhe) != 'F') {
            return false;
        }

        $d = $this->detalheAtual();

        $d->setDataCredito($this->rem(45, 52, $detalhe), 'Ymd')
          ->setValor(Util::nFloat($this->rem(53, 67, $detalhe)/100, 2, false))
          ->setOcorrencia($this->rem(68, 69, $detalhe))
          ->setOcorrenciaDescricao(Arr::get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'))
          ->setNumeroDocumento($this->rem(70, 88, $detalhe));

        if ($d->hasOcorrencia('00', '31')) {
            $this->totais['liquidados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA)
            ->setValorRecebido(Util::nFloat($this->rem(53, 67, $detalhe)/100, 2, false));

        } elseif ($d->hasOcorrencia('01', '02', '05', '10', '12','13','14','15', '18', '30')) {
            $this->totais['erros']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ERRO);
        } else {
            $this->totais['outros']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_OUTROS);
        }

        return true;
    }

    /**
     * @param array $trailer
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarTrailer(array $trailer)
    {
        $this->getTrailer()
            ->setQuantidadeTitulos((int) $this->rem(2, 7, $trailer))
            ->setValorTitulos(Util::nFloat($this->rem(8, 24, $trailer)/100, 2, false));

        return true;
    }
}
