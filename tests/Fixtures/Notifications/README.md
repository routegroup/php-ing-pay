# Recorded notifications

Bodies the ING Pay sandbox really sent, caught byte for byte in October 2026.

The merchant and service they were issued for, and the tunnel host they were
delivered to, are replaced with fixture values. **The signature is recomputed
under `fixture-service-key`** — it is not the one the gateway produced.
Publishing a real body beside its real signature would hand out an offline
oracle for the service key, since verification is `hash(body . serviceKey)`.

The identity these verify against lives in `recordedConfig()`.
