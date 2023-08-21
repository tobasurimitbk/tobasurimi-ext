<?php

namespace App\Controllers\Penerimaan;

use App\Controllers\BaseController;

use App\Models\CustomerModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderPaymentModel;

class SalesOrderPayment extends BaseController
{
    private $customerModel;
    private $salesOrderModel;
    private $salesOrderPaymentModel;

    private $companyId;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->salesOrderModel = new SalesOrderModel();
        $this->salesOrderPaymentModel = new SalesOrderPaymentModel();

        $this->companyId = session()->get("login")->this_company_id;
    }
    

    public function index()
    {
        return view('Penerimaan/Local/index');
    }

    public function create()
    {
        $customerList = $this->customerModel->asObject()
            ->where('company_id', $this->companyId)
            ->findAll();

        $data = [
            'customerList'=> $customerList
        ];
        return view('Penerimaan/Local/form', $data);
    }

    public function save()
    {
        try {
            $postData = $this->request->getPost();
            $postData["paid_invoices"] = json_decode($postData["paid_invoices"], true);

            $rules = [
                "customer_id" => [
                    "rules" => "required|is_natural"
                ],
                "payment_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "rate" => [
                    "rules" => "required|numeric"
                ],
                "cheque_no" => [
                    "rules" => "required"
                ],
                "cheque_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "dept" => [
                    "rules" => "required"
                ],
                "memo" => [
                    "rules" => "required"
                ],
                "paid_invoices.*.id" => [
                    "rules" => "required|is_natural"
                ],
                "paid_invoices.*.payment_amt" => [
                    "rules" => "required|numeric"
                ]
            ];
    
            if (!$this->validateData($postData, $rules)) {}

            $customerId = $postData['customer_id'];

            $customerData = $this->customerModel->asObject()
                ->find($customerId);
            if (empty($customerData)) {}

            foreach ($postData['paid_invoices'] as $paidInv) {
                $invData = $this->salesOrderModel->asObject()
                    ->find($paidInv['id']);

                if (empty($invData)) {}
                
                $paidInv['payment_amt'];
            }

            $this->salesOrderPaymentModel->db->transException(true)->transStart();
            $this->salesOrderPaymentModel->db->transComplete();

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
