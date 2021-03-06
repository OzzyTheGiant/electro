/*
 * This file is generated by jOOQ.
*/
package jooq.generated.tables.records;


import java.math.BigDecimal;
import java.sql.Date;
import javax.annotation.Generated;
import jooq.generated.tables.Bills;
import org.jooq.Field;
import org.jooq.Record1;
import org.jooq.Record4;
import org.jooq.Row4;
import org.jooq.impl.UpdatableRecordImpl;


/**
 * This class is generated by jOOQ.
 */
@Generated(
    value = {
        "http://www.jooq.org",
        "jOOQ version:3.10.1"
    },
    comments = "This class is generated by jOOQ"
)
@SuppressWarnings({ "all", "unchecked", "rawtypes" })
public class BillsRecord extends UpdatableRecordImpl<BillsRecord> implements Record4<Integer, BigDecimal, Date, Integer> {

    private static final long serialVersionUID = 1051972273;

    /**
     * Setter for <code>Electro.Bills.ID</code>.
     */
    public void setId(Integer value) {
        set(0, value);
    }

    /**
     * Getter for <code>Electro.Bills.ID</code>.
     */
    public Integer getId() {
        return (Integer) get(0);
    }

    /**
     * Setter for <code>Electro.Bills.PaymentAmount</code>.
     */
    public void setPaymentamount(BigDecimal value) {
        set(1, value);
    }

    /**
     * Getter for <code>Electro.Bills.PaymentAmount</code>.
     */
    public BigDecimal getPaymentamount() {
        return (BigDecimal) get(1);
    }

    /**
     * Setter for <code>Electro.Bills.PaymentDate</code>.
     */
    public void setPaymentdate(Date value) {
        set(2, value);
    }

    /**
     * Getter for <code>Electro.Bills.PaymentDate</code>.
     */
    public Date getPaymentdate() {
        return (Date) get(2);
    }

    /**
     * Setter for <code>Electro.Bills.User</code>.
     */
    public void setUser(Integer value) {
        set(3, value);
    }

    /**
     * Getter for <code>Electro.Bills.User</code>.
     */
    public Integer getUser() {
        return (Integer) get(3);
    }

    // -------------------------------------------------------------------------
    // Primary key information
    // -------------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    @Override
    public Record1<Integer> key() {
        return (Record1) super.key();
    }

    // -------------------------------------------------------------------------
    // Record4 type implementation
    // -------------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    @Override
    public Row4<Integer, BigDecimal, Date, Integer> fieldsRow() {
        return (Row4) super.fieldsRow();
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public Row4<Integer, BigDecimal, Date, Integer> valuesRow() {
        return (Row4) super.valuesRow();
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public Field<Integer> field1() {
        return Bills.BILLS.ID;
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public Field<BigDecimal> field2() {
        return Bills.BILLS.PAYMENTAMOUNT;
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public Field<Date> field3() {
        return Bills.BILLS.PAYMENTDATE;
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public Field<Integer> field4() {
        return Bills.BILLS.USER;
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public Integer component1() {
        return getId();
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public BigDecimal component2() {
        return getPaymentamount();
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public Date component3() {
        return getPaymentdate();
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public Integer component4() {
        return getUser();
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public Integer value1() {
        return getId();
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public BigDecimal value2() {
        return getPaymentamount();
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public Date value3() {
        return getPaymentdate();
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public Integer value4() {
        return getUser();
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public BillsRecord value1(Integer value) {
        setId(value);
        return this;
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public BillsRecord value2(BigDecimal value) {
        setPaymentamount(value);
        return this;
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public BillsRecord value3(Date value) {
        setPaymentdate(value);
        return this;
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public BillsRecord value4(Integer value) {
        setUser(value);
        return this;
    }

    /**
     * {@inheritDoc}
     */
    @Override
    public BillsRecord values(Integer value1, BigDecimal value2, Date value3, Integer value4) {
        value1(value1);
        value2(value2);
        value3(value3);
        value4(value4);
        return this;
    }

    // -------------------------------------------------------------------------
    // Constructors
    // -------------------------------------------------------------------------

    /**
     * Create a detached BillsRecord
     */
    public BillsRecord() {
        super(Bills.BILLS);
    }

    /**
     * Create a detached, initialised BillsRecord
     */
    public BillsRecord(Integer id, BigDecimal paymentamount, Date paymentdate, Integer user) {
        super(Bills.BILLS);

        set(0, id);
        set(1, paymentamount);
        set(2, paymentdate);
        set(3, user);
    }
}
