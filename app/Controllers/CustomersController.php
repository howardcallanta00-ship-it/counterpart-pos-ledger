<?php
namespace App\Controllers;
use App\Models\CustomerModel;
use CodeIgniter\Exceptions\PageNotFoundException;
final class CustomersController extends BaseController
{
    public function index(): string { return view('customers/index', ['title' => 'Customers', 'customers' => (new CustomerModel())->orderBy('full_name')->findAll()]); }
    public function new(): string { return view('customers/form', ['title' => 'New customer', 'customer' => null]); }
    public function create()
    {
        $model = new CustomerModel();
        if (! $model->insert($this->data())) { return redirect()->back()->withInput()->with('errors', $model->errors()); }
        return redirect()->to('/customers')->with('success', 'Customer added to the ledger.');
    }
    public function edit(int $id): string
    {
        $customer = (new CustomerModel())->find($id);
        if (! $customer) { throw PageNotFoundException::forPageNotFound('Customer not found.'); }
        return view('customers/form', ['title' => 'Edit customer', 'customer' => $customer]);
    }
    public function update(int $id)
    {
        $model = new CustomerModel();
        if (! $model->find($id)) { throw PageNotFoundException::forPageNotFound('Customer not found.'); }
        if (! $model->update($id, $this->data())) { return redirect()->back()->withInput()->with('errors', $model->errors()); }
        return redirect()->to('/customers')->with('success', 'Customer details updated.');
    }
    private function data(): array
    {
        return ['full_name' => preg_replace('/\s+/u', ' ', trim((string) $this->request->getPost('full_name'))), 'email' => mb_strtolower(trim((string) $this->request->getPost('email'))), 'phone' => trim((string) $this->request->getPost('phone')) ?: null];
    }
}
