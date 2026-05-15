<div class="grid grid-cols-2 gap-4">

    <div>
        <label>วันที่</label>

        <input type="date"
               name="expense_date"
               value="{{ old('expense_date', $expense->expense_date ?? date('Y-m-d')) }}"
               class="w-full border rounded-lg p-2">
    </div>

    <div>
        <label>ผู้จำหน่าย</label>

        <select name="payee_id"
                class="w-full border rounded-lg p-2">

            <option value="">-- เลือก --</option>

            @foreach($payees as $payee)
                <option value="{{ $payee->id }}"
                    @selected(old('payee_id', $expense->payee_id ?? '') == $payee->id)>
                    {{ $payee->name }}
                </option>
            @endforeach

        </select>
    </div>

    <div class="col-span-2">
        <label>รายละเอียด</label>

        <textarea name="description"
                  class="w-full border rounded-lg p-2"
                  rows="3">{{ old('description', $expense->description ?? '') }}</textarea>
    </div>

    <div>
        <label>จำนวนเงิน</label>

        <input type="number"
               step="0.01"
               name="amount"
               value="{{ old('amount', $expense->amount ?? 0) }}"
               class="w-full border rounded-lg p-2">
    </div>
<div>
    <label class="block font-medium mb-1">ผังบัญชี</label>

    <select name="account_id"
            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">

        <option value="">-- เลือกผังบัญชี --</option>

        @foreach($accounts as $acc)
            <option value="{{ $acc->id }}" {{ old('account_id', $expense->account_id ?? '') == $acc->id ? 'selected' : '' }}>
                {{ $acc->code }} - {{ $acc->name_th }}
            </option>
        @endforeach

    </select>
</div>
    <div class="col-span-2">
        <label>หมายเหตุ</label>

        <textarea name="remark"
                  class="w-full border rounded-lg p-2"
                  rows="2">{{ old('remark', $expense->remark ?? '') }}</textarea>
    </div>

</div>
