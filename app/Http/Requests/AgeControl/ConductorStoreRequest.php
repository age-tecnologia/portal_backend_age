<?php

namespace App\Http\Requests\AgeControl;

use Illuminate\Foundation\Http\FormRequest;

class ConductorStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'firstName' => 'required|min:3',
            'lastName' => 'required|min:5',
            'address' => 'required|min:5',
            'city' => 'required|integer',
            'email' => 'required|email',
            'group' => 'required|integer',
            'typeService' => 'required|integer',
            'typeVehicle' => 'required|integer',
            'modality' => 'required|integer',
            'manufacturer' => 'required',
            'model' => 'required',
            'tankCapacity' => 'required|numeric',
            'averageKmL' => 'required|numeric',
            'initialKm' => 'required|numeric',
            'distanceBaseHouse' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'firstName.required' => 'O primeiro nome é obrigatório ser enviado.',
            'firstName.min' => 'O primeiro nome precisa ter ao menos 3 caracteres.',
            'lastName.required' => 'O sobrenome é obrigatório ser enviado.',
            'lastName.min' => 'O sobrenome precisa ter ao menos 5 caracteres.',
            'address.required' => 'O endereço é obrigatório ser enviado.',
            'address.min' => 'O endereço precisa ter ao menos 5 caracteres.',
            'city.required' => 'A cidade é obrigatório ser selecionada.',
            'city.integer' => 'A cidade precisa ser um integer {id}.',
            'email.required' => 'O email é obrigatório ser enviado.',
            'email.email' => 'O email precisa ser um email válido.',
            'group.required' => 'O grupo é obrigatório ser selecionado.',
            'group.integer' => 'O grupo precisa ser um integer {id}.',
            'typeService.required' => 'O tipo de serviço é obrigatório ser selecionado.',
            'typeService.integer' => 'O tipo de serviço precisa ser um integer {id}.',
            'typeVehicle.required' => 'O tipo de veículo é obrigatório ser selecionado.',
            'typeVehicle.integer' => 'O tipo de veículo precisa ser um integer {id}.',
            'modality.required' => 'A modalidade é obrigatório ser selecionada.',
            'modality.integer' => 'A modalidade precisa ser um integer {id}.',
            'manufacturer.required' => 'O fabricante é obrigatório ser enviado.',
            'model.required' => 'O modelo é obrigatório ser enviado.',
            'tankCapacity.required' => 'A capacidade do tanque é obrigatório ser enviado.',
            'tankCapacity.numeric' => 'A capacidade do tanque precisa ser um valor numérico.',
            'averageKmL.required' => 'O consumo médio é obrigatório ser enviado.',
            'averageKmL.numeric' => 'O consumo médio precisa ser um valor numérico.',
            'initialKm.required' => 'A quilometragem inicial é obrigatório ser enviada.',
            'initialKm.numeric' => 'A quilometragem inicial precisa ser um valor numérico.',
            'distanceBaseHouse.required' => 'A distância entre sede e casa é obrigatório ser enviada.',
            'distanceBaseHouse.numeric' => 'A distância entre sede e casa precisa ser um valor numérico.',
        ];
    }
}
