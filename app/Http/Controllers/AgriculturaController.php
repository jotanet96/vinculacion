<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateAgriculturaRequest;
use App\Http\Requests\UpdateAgriculturaRequest;
use App\Repositories\AgriculturaRepository;
use Flash;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use \App\Models\unidadproduccion;
use \App\Models\UsoSuelo;

class AgriculturaController extends AppBaseController
{
    /** @var  AgriculturaRepository */
    private $agriculturaRepository;

    public function __construct(AgriculturaRepository $agriculturaRepo)
    {
        $this->agriculturaRepository = $agriculturaRepo;
    }

    /**
     * Display a listing of the Agricultura.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->agriculturaRepository->pushCriteria(new RequestCriteria($request));
        $agriculturas = $this->agriculturaRepository->all();

        return view('agriculturas.index')
            ->with('agriculturas', $agriculturas);
    }

    /**
     * Show the form for creating a new Agricultura.
     *
     * @return Response
     */
    public function create()
    {
        $UsoSuelo         = UsoSuelo::all()->pluck('nombre', 'id');
        $unidadproduccion = unidadproduccion::all()->pluck('nombre', 'id');
        return view('agriculturas.create', ['UsoSuelo' => $UsoSuelo, 'unidadproduccion' => $unidadproduccion]);
    }

    /**
     * Store a newly created Agricultura in storage.
     *
     * @param CreateAgriculturaRequest $request
     *
     * @return Response
     */
    public function store(CreateAgriculturaRequest $request)
    {
        $input = $request->all();

        $agricultura = $this->agriculturaRepository->create($input);

        Flash::success('Agricultura guardado exitosamente.');

        return redirect(route('agriculturas.index'));
    }

    /**
     * Display the specified Agricultura.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $agricultura = $this->agriculturaRepository->findWithoutFail($id);

        if (empty($agricultura)) {
            Flash::error('Agricultura not found');

            return redirect(route('agriculturas.index'));
        }

        return view('agriculturas.show')->with('agricultura', $agricultura);
    }

    /**
     * Show the form for editing the specified Agricultura.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {

        $UsoSuelo         = UsoSuelo::all()->pluck('nombre', 'id');
        $unidadproduccion = unidadproduccion::all()->pluck('nombre', 'id');
        $agricultura      = $this->agriculturaRepository->findWithoutFail($id);

        if (empty($agricultura)) {
            Flash::error('Agricultura not found');

            return redirect(route('agriculturas.index'));
        }

        return view('agriculturas.edit')->with('agricultura', $agricultura)->with('UsoSuelo', $UsoSuelo)->with('unidadproduccion', $unidadproduccion);
    }

    /**
     * Update the specified Agricultura in storage.
     *
     * @param  int              $id
     * @param UpdateAgriculturaRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAgriculturaRequest $request)
    {
        $agricultura = $this->agriculturaRepository->findWithoutFail($id);

        if (empty($agricultura)) {
            Flash::error('Agricultura not found');

            return redirect(route('agriculturas.index'));
        }

        $agricultura = $this->agriculturaRepository->update($request->all(), $id);

        Flash::success('Agricultura updated successfully.');

        return redirect(route('agriculturas.index'));
    }

    /**
     * Remove the specified Agricultura from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $agricultura = $this->agriculturaRepository->findWithoutFail($id);

        if (empty($agricultura)) {
            Flash::error('Agricultura not found');

            return redirect(route('agriculturas.index'));
        }

        $this->agriculturaRepository->delete($id);

        Flash::success('Agricultura deleted successfully.');

        return redirect(route('agriculturas.index'));
    }
}
