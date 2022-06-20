<?php

namespace LaravelReady\LicenseServer\Http\Controllers\Api;

use Illuminate\Http\Request;
use LaravelReady\LicenseServer\Models\License;
use LaravelReady\LicenseServer\Http\Requests\LicenseUpdateRequest;
use LaravelReady\LicenseServer\Http\Controllers\BaseController;

class LicenseController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->query('q');
        $perPage = $request->query('perPage', 15);
        $perPage = $perPage > 100 ? 100 : $perPage;

        $licenseQuery = License::with([
            'licensedTo',
        ]);

        if ($search) {
            $licenseQuery->where('domain', 'like', "%{$search}%");
        }

        $resource = $licenseQuery->simplePaginate($perPage);

        return [
            'success' => true,
            'result' => $resource,
        ];
    }

    /**
     * Not implemented store method.
     */
    public function store()
    {
        return [
            'success' => false,
            'result' => 'You can not create a new license directly. Please use service class.',
        ];
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $resource = License::findOrFail($id);

        return [
            'success' => true,
            'result' => $resource,
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \LaravelReady\LicenseServer\Http\Requests\LicenseUpdateRequest $request
     * @param \LaravelReady\LicenseServer\Models\License $theme
     * @return \Illuminate\Http\Response
     */
    public function update(LicenseUpdateRequest $request, License $license)
    {
        $data = $request->validated();

        $result = $license->update($data);

        return [
            'success' => $result,
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \LaravelReady\LicenseServer\Models\License $theme
     * @return \Illuminate\Http\Response
     */
    public function destroy(License $license)
    {
        $result = $license->delete();

        return [
            'success' => $result,
        ];
    }
}
