import Validators from "@app/services/validators"
import { EmptyRequestBodyError } from "@app/exceptions"

export default class Bill {
    public id!: number
    public user_id!: number
    public payment_amount!: number
    public payment_date!: string

    public constructor(props: Partial<Bill>) {
        Object.assign(this, props)
    }

    static getValidationHandlers(): { [key: string]: CallableFunction[] } {
        return {
            user_id: [Validators.required],
            payment_amount: [Validators.required, Validators.max(99999.99)],
            payment_date: [Validators.required, Validators.isDate]
        }
    }

    static withValidatedData(billData: Partial<Bill>): Bill {
        const validators = Bill.getValidationHandlers()
        const data = billData as { [key: string]: any }

        if (Object.keys(data).length === 0) throw new EmptyRequestBodyError()

        for (const prop in validators) {
            // validate prop using the specified validators
            for (const validator of validators[prop]) {
                if (typeof data[prop] === "string") data[prop] = data[prop].trim()
                validator(prop, data[prop])
            }
        }

        return new Bill(data)
    }
}

module.exports = Bill;
