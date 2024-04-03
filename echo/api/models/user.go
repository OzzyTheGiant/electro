package models

import (
	"errors"

	"github.com/alexedwards/argon2id"
)

type User struct {
	ID       int     `json:"id"`
	Username string  `json:"username" gorm:"type:varchar(255);unique;not null"`
	Password *string `json:"password" gorm:"type:varchar(255);not null;"`
}

func (user *User) ToMap(withHiddenFields bool) map[string]interface{} {
	data := map[string]interface{}{
		"id":       user.ID,
		"username": user.Username,
	}

	if withHiddenFields {
		data["password_hash"] = user.Password
	}

	return data
}

func (user *User) HashPassword() (err error) {
	hash, err := argon2id.CreateHash(*user.Password, argon2id.DefaultParams)

	if err != nil {
		return errors.New("Server Error: Unable to encrypt password")
	}

	user.Password = &hash
	return
}
