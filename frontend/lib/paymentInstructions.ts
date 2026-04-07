import type { PaymentMethod } from "./types";

export const PAYMENT_OPTIONS: { id: PaymentMethod }[] = [
  { id: "atm_transfer" },
  { id: "pay_id" },
  { id: "bpay" },
];

/** Demo-only credentials — placeholders, not real accounts. */
export function paymentDetailBlocks(
  method: PaymentMethod,
  orderReference: string,
  totalFormatted: string,
  t?: (key: string) => string,
): { title: string; lines: { label: string; value: string }[] }[] {
  const tr = (key: string, fallback: string) => (t ? t(key) : fallback);
  const refNote = tr("payDetail.quoteRef", "Quote reference: {reference}").replace(
    "{reference}",
    orderReference,
  );
  const amountLine = {
    label: tr("payDetail.amountDue", "Amount due"),
    value: totalFormatted,
  };

  switch (method) {
    case "atm_transfer":
      return [
        {
          title: tr("payDetail.atmTitle", "Bank transfer details (demo)"),
          lines: [
            amountLine,
            {
              label: tr("payDetail.accountName", "Account name"),
              value: tr("payDetail.demoCompany", "Edward's Store Demo Pty Ltd"),
            },
            { label: tr("payDetail.bsb", "BSB"), value: "062-000" },
            {
              label: tr("payDetail.accountNumber", "Account number"),
              value: "1234 5678",
            },
            {
              label: tr("payDetail.referenceNarration", "Reference / narration"),
              value: orderReference,
            },
            {
              label: tr("payDetail.note", "Note"),
              value: tr(
                "payDetail.atmNote",
                "Demo only. Do not send real money to these details.",
              ),
            },
          ],
        },
      ];
    case "pay_id":
      return [
        {
          title: tr("payDetail.payidTitle", "PayID (demo)"),
          lines: [
            amountLine,
            { label: tr("payDetail.payidType", "PayID type"), value: tr("payDetail.typeEmail", "Email") },
            {
              label: tr("payDetail.payid", "PayID"),
              value: "payid.demo@edwards-store.demo",
            },
            {
              label: tr("payDetail.description", "Description"),
              value: refNote,
            },
            {
              label: tr("payDetail.note", "Note"),
              value: tr(
                "payDetail.payidNote",
                "Demo only. This PayID is not registered for real payments.",
              ),
            },
          ],
        },
      ];
    case "bpay":
      return [
        {
          title: tr("payDetail.bpayTitle", "BPAY (demo)"),
          lines: [
            amountLine,
            { label: tr("payDetail.billerCode", "Biller code"), value: "12345" },
            {
              label: tr("payDetail.customerRef", "Customer reference"),
              value:
                orderReference.replace(/\D/g, "").slice(-10) || "0000000001",
            },
            { label: tr("payDetail.note", "Note"), value: refNote },
            {
              label: tr("payDetail.disclaimer", "Disclaimer"),
              value: tr(
                "payDetail.bpayDisclaimer",
                "Placeholder only. Use real BPAY details only from genuine invoices.",
              ),
            },
          ],
        },
      ];
    default:
      return [];
  }
}
