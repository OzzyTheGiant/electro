package models

type Bill struct {
	ID            int     `json:"id"`
	UserID        int     `json:"user_id" gorm:"type:int;not null;constraint:OnUpdate:CASCADE,OnDelete:SET NULL;" validate:"required"`
	PaymentAmount float32 `json:"payment_amount" gorm:"type:double(5,2);not null" validate:"required"`
	PaymentDate   string  `json:"payment_date" gorm:"type:date;not null;" validate:"required"`
}

type BillList []Bill

func (bill Bill) ToMap() map[string]interface{} {
	return map[string]interface{}{
		"id":             bill.ID,
		"user_id":        bill.UserID,
		"payment_amount": bill.PaymentAmount,
		"payment_date":   bill.PaymentDate,
	}
}
