import { ValidationError } from "@app/exceptions"

export default class Validators {
    static required(key: string, value: any) {
        if ((typeof value === "string" && value === "") || value === undefined) {
            throw new ValidationError(key, ValidationError.types.required)
        } return true
    }

    static max(maxLength: number) {
        return (key: string, value: any) => {
            if (typeof value === "string" && value.length > maxLength) {
                throw new ValidationError(key, ValidationError.types.maxStringSize)
            } else if (typeof value === "number" && value > maxLength) {
                throw new ValidationError(key, ValidationError.types.maxNumberSize)
            } return true
        }
    }

    static isDate(key: string, value: any) {
        if (new Date(value).toString() === "Invalid Date") {
            throw new ValidationError(key, ValidationError.types.isDate)
        } return true
    }
}
