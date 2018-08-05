<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAreaInfluenciaRequest;
use App\Http\Requests\UpdateAreaInfluenciaRequest;
use App\Repositories\AreaInfluenciaRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Barryvdh\DomPDF\Facade as PDF;
class AreaInfluenciaController extends AppBaseController
{
    /** @var  AreaInfluenciaRepository */
    private $areaInfluenciaRepository;

    public function __construct(AreaInfluenciaRepository $areaInfluenciaRepo)
    {
        $this->areaInfluenciaRepository = $areaInfluenciaRepo;
    }

    /**
     * Display a listing of the AreaInfluencia.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->areaInfluenciaRepository->pushCriteria(new RequestCriteria($request));
        $areaInfluencias = $this->areaInfluenciaRepository->all();  
        return view('area_influencias.index')
            ->with('areaInfluencias', $areaInfluencias)
            ->with('chart',$this->createChart($areaInfluencias, $request->get('selectareaInfluencia')));
    }

    /**
     * Show the form for creating a new AreaInfluencia.
     *
     * @return Response
     */
    public function create()
    {
        return view('area_influencias.create');
    }

    /**
     * Store a newly created AreaInfluencia in storage.
     *
     * @param CreateAreaInfluenciaRequest $request
     *
     * @return Response
     */
    public function store(CreateAreaInfluenciaRequest $request)
    {
        $input = $request->all();

        $areaInfluencia = $this->areaInfluenciaRepository->create($input);

        Flash::success('Area Influencia saved successfully.');

        return redirect(route('areaInfluencias.index'));
    }

    /**
     * Display the specified AreaInfluencia.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $areaInfluencia = $this->areaInfluenciaRepository->findWithoutFail($id);

        if (empty($areaInfluencia)) {
            Flash::error('Area Influencia not found');

            return redirect(route('areaInfluencias.index'));
        }

        return view('area_influencias.show')->with('areaInfluencia', $areaInfluencia);
    }

    /**
     * Show the form for editing the specified AreaInfluencia.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $areaInfluencia = $this->areaInfluenciaRepository->findWithoutFail($id);

        if (empty($areaInfluencia)) {
            Flash::error('Area Influencia not found');

            return redirect(route('areaInfluencias.index'));
        }

        return view('area_influencias.edit')->with('areaInfluencia', $areaInfluencia);
    }

    /**
     * Update the specified AreaInfluencia in storage.
     *
     * @param  int              $id
     * @param UpdateAreaInfluenciaRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAreaInfluenciaRequest $request)
    {
        $areaInfluencia = $this->areaInfluenciaRepository->findWithoutFail($id);

        if (empty($areaInfluencia)) {
            Flash::error('Area Influencia not found');

            return redirect(route('areaInfluencias.index'));
        }

        $areaInfluencia = $this->areaInfluenciaRepository->update($request->all(), $id);

        Flash::success('Area Influencia updated successfully.');

        return redirect(route('areaInfluencias.index'));
    }

    /**
     * Remove the specified AreaInfluencia from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $areaInfluencia = $this->areaInfluenciaRepository->findWithoutFail($id);

        if (empty($areaInfluencia)) {
            Flash::error('Area Influencia not found');

            return redirect(route('areaInfluencias.index'));
        }

        $this->areaInfluenciaRepository->delete($id);

        Flash::success('Area Influencia deleted successfully.');

        return redirect(route('areaInfluencias.index'));
    }
    public function areaInfluenciasHTMLPDF(Request $request)
    {
        $productos = $this->areaInfluenciaRepository->all();//OBTENGO TODOS MIS PRODUCTO
        view()->share('areainfluencias',$productos);//VARIABLE GLOBAL PRODUCTOS
        if($request->has('descargar')){
            $pdf = PDF::loadView('pdf.tablaInfluencias',compact('productos'));//CARGO LA VISTA
            return $pdf->stream('areaInfluencias.pdf');//SUGERIR NOMBRE A DESCARGAR
        }
        return view('areaInfluencias-pdf');//RETORNO A MI VISTA
    }
    
    
public function createChart($areaInfluencias, $tipoVariable){
             $preprocessedDataset = $areaInfluencias->sortBy('nombre');

      $dataset = collect();
        switch ($tipoVariable) {                    
             case '0':
                foreach ($preprocessedDataset as $areainfluencias) {
                    $temp = [
                      'nombre' => (string)$areainfluencias->manejoambiental->nombre,
                      'tipoTerrenoDescripcion' =>(string) $areainfluencias->tipoTerrenoDescripcion,
                      'TipoSuelo_id' =>(string)$areainfluencias->tiposuelo->nombre
                    ];
                    $dataset->push($temp);
                  }
                $dataset = $dataset->groupBy('nombre');
                $dataset = $dataset->map(function ($item) {
                    return $item->groupBy('TipoSuelo_id')->map(function ($item2){
                        return $item2->count('tipoTerrenoDescripcion');
                    });
                });
                    //dd($asociacions);
                $labels = $dataset->collapse()->toArray();
                $dataset = $dataset->toArray();
                $labels = array_fill_keys(array_keys($labels), 0);
                $chart = new DefaultChart;
                foreach ($dataset as $key => $item) {
                    $chart->dataset($key, 'column', array_values(array_merge($labels, $item)));
                }
                $chart->labels(array_keys($labels));
                $chart->title('Área de Influencia');
                $chart->label("Número de áreas de Influencias");
                return $chart;
                break;
            
            case '1':
                foreach ($preprocessedDataset as $areainfluencias) {
                    $temp = [
                        'nombre' => (string)$areainfluencias->permeabilidadsuelo->nombre,
                        'tipoTerrenoDescripcion' =>(string) $areainfluencias->tipoTerrenoDescripcion,
                        'TipoSuelo_id' =>(string)$areainfluencias->tiposuelo->nombre
                    ];
                    $dataset->push($temp);
                }
                $dataset = $dataset->groupBy('nombre');
                $dataset = $dataset->map(function ($item) {
                    return $item->groupBy('TipoSuelo_id')->map(function ($item2){
                        return $item2->count('tipoTerrenoDescripcion');
                    });
                });
                    //dd($asociacions);
                $labels = $dataset->collapse()->toArray();
                $dataset = $dataset->toArray();
                $labels = array_fill_keys(array_keys($labels), 0);
                $chart = new DefaultChart;
                foreach ($dataset as $key => $item) {
                    $chart->dataset($key, 'column', array_values(array_merge($labels, $item)));
                }
                $chart->labels(array_keys($labels));
                $chart->title('Área de Influencia');
                $chart->label("Número de áreas de Influencias");
                return $chart;
                break;
            
            case '2':
                foreach ($preprocessedDataset as $areainfluencias) {
                    $temp = [
                        'nombre' => (string)$areainfluencias->clima->nombre,
                        'tipoTerrenoDescripcion' =>(string) $areainfluencias->tipoTerrenoDescripcion,
                        'TipoSuelo_id' =>(string)$areainfluencias->tiposuelo->nombre
                    ];
                    $dataset->push($temp);
                }
                $dataset = $dataset->groupBy('nombre');
                $dataset = $dataset->map(function ($item) {
                    return $item->groupBy('TipoSuelo_id')->map(function ($item2){
                        return $item2->count('tipoTerrenoDescripcion');
                    });
                });
                    //dd($asociacions);
                $labels = $dataset->collapse()->toArray();
                $dataset = $dataset->toArray();
                $labels = array_fill_keys(array_keys($labels), 0);
                $chart = new DefaultChart;
                foreach ($dataset as $key => $item) {
                    $chart->dataset($key, 'column', array_values(array_merge($labels, $item)));
                }
                $chart->labels(array_keys($labels));
                $chart->title('Área de Influencia');
                $chart->label("Número de áreas de Influencias");
                return $chart;
                break;
            
            case '3':
                foreach ($preprocessedDataset as $areainfluencias) {
                    $temp = [
                        'nombre' => (string)$areainfluencias->condicionesdrenaje->nombre,
                        'tipoTerrenoDescripcion' =>(string) $areainfluencias->tipoTerrenoDescripcion,
                        'TipoSuelo_id' =>(string)$areainfluencias->tiposuelo->nombre
                    ];
                    $dataset->push($temp);
                }
                $dataset = $dataset->groupBy('nombre');
                $dataset = $dataset->map(function ($item) {
                    return $item->groupBy('TipoSuelo_id')->map(function ($item2){
                        return $item2->count('tipoTerrenoDescripcion');
                    });
                });
                    //dd($asociacions);
                $labels = $dataset->collapse()->toArray();
                $dataset = $dataset->toArray();
                $labels = array_fill_keys(array_keys($labels), 0);
                $chart = new DefaultChart;
                foreach ($dataset as $key => $item) {
                    $chart->dataset($key, 'column', array_values(array_merge($labels, $item)));
                }
                $chart->labels(array_keys($labels));
                $chart->title('Área de Influencia');
                $chart->label("Número de áreas de Influencias");
                return $chart;
                break;
            
            case '4':
                 foreach ($preprocessedDataset as $areainfluencias) {
                     $temp = [
                         'nombre' => (string)$areainfluencias->ecosistema->nombre,
                         'tipoTerrenoDescripcion' =>(string) $areainfluencias->tipoTerrenoDescripcion,
                         'TipoSuelo_id' =>(string)$areainfluencias->tiposuelo->nombre
                     ];
                     $dataset->push($temp);
                 }
                $dataset = $dataset->groupBy('nombre');
                $dataset = $dataset->map(function ($item) {
                    return $item->groupBy('TipoSuelo_id')->map(function ($item2){
                        return $item2->count('tipoTerrenoDescripcion');
                    });
                });
                    //dd($asociacions);
                $labels = $dataset->collapse()->toArray();
                $dataset = $dataset->toArray();
                $labels = array_fill_keys(array_keys($labels), 0);
                $chart = new DefaultChart;
                foreach ($dataset as $key => $item) {
                    $chart->dataset($key, 'column', array_values(array_merge($labels, $item)));
                }
                $chart->labels(array_keys($labels));
                $chart->title('Área de Influencia');
                $chart->label("Número de áreas de Influencias");
                return $chart;
                break;
            
            default:
                foreach ($preprocessedDataset as $areainfluencias) {
                    $temp = [
                      'nombre' => (string)$areainfluencias->manejoambiental->nombre,
                      'tipoTerrenoDescripcion' =>(string) $areainfluencias->tipoTerrenoDescripcion,
                      'TipoSuelo_id' =>(string)$areainfluencias->tiposuelo->nombre
                    ];
                    $dataset->push($temp);
                  }
                $dataset = $dataset->groupBy('nombre');
                $dataset = $dataset->map(function ($item) {
                    return $item->groupBy('TipoSuelo_id')->map(function ($item2){
                        return $item2->count('tipoTerrenoDescripcion');
                    });
                });
                    //dd($asociacions);
                $labels = $dataset->collapse()->toArray();
                $dataset = $dataset->toArray();
                $labels = array_fill_keys(array_keys($labels), 0);
                $chart = new DefaultChart;
                foreach ($dataset as $key => $item) {
                    $chart->dataset($key, 'column', array_values(array_merge($labels, $item)));
                }
                $chart->labels(array_keys($labels));
                $chart->title('Área de Influencia');
                $chart->label("Número de áreas de Influencias");
                return $chart;
                break;
        }
    }
}
